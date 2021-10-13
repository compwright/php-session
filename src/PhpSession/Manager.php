<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession;

class Manager
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SessionId
     */
    protected $sid;

    /**
     * @var Session
     */
    protected $currentSession;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->sid = new SessionId($config);
    }

    /**
     * Get the session config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get the current session
     */
    public function getCurrentSession(): ?Session
    {
        return $this->currentSession;
    }

    /**
     * Discard session array changes and finish session
     */
    public function abort(): bool
    {
        if (!$this->currentSession) {
            return false;
        }

        $this->currentSession->close();
        return !!$this->config->getSaveHandler()->close();
    }

    /**
     * Write session changes
     */
    public function commit(): bool
    {
        if (!$this->currentSession) {
            return false;
        }

        $handler = $this->config->getSaveHandler();
        $id = $this->currentSession->getId();
        $contents = $this->encode();

        if ($contents === false) {
            $handler->destroy($id);
            $handler->close();
            $this->currentSession->close();
            return false;
        }
        
        if (!$this->currentSession->isModified() && $this->config->getLazyWrite()) {
            return false;
        }

        if ($handler instanceof Handlers\SessionCasHandlerInterface) {
            $token = $this->currentSession->getCasToken();
            return $handler->write_cas($token, $id, $contents);
        }

        return $handler->write($id, $contents);
    }

    /**
     * Create new session id
     */
    public function create_id(string $prefix = "")
    {
        if ($prefix && preg_match("/^[a-zA-Z0-9,-]+$/", $prefix) === 0) {
            throw new \InvalidArgumentException("\$prefix contains disallowed characters");
        }

        $this->config->setSidPrefix($prefix);

        $handler = $this->config->getSaveHandler();

        if ($handler instanceof \SessionIdInterface) {
            return $handler->create_sid();
        }

        $id = $this->sid->create_sid();

        return $id;
    }

    /**
     * Decodes session data from a session encoded string
     */
    public function decode(string $data)
    {
        try {
            $serializer = $this->config->getSerializeHandler();
            $this->currentSession->setContents($serializer->unserialize($data));
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Destroys all data registered to a session
     */
    public function destroy(): bool
    {
        return !!$this->config
            ->getSaveHandler()
            ->destroy($this->currentSession->getId());
    }

    /**
     * Encodes the current session data as a session encoded string
     */
    public function encode()
    {
        try {
            $serializer = $this->config->getSerializeHandler();
            return $serializer->serialize($this->currentSession->toArray());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Perform session data garbage collection
     */
    public function gc()
    {
        return $this->config
            ->getSaveHandler()
            ->gc($this->config->getGcMaxLifetime());
    }

    /**
     * Get and/or set the current session id
     */
    public function id(string $id = null): string
    {
        $returnId = $this->currentSession
            ? $this->currentSession->getId()
            : "";

        if (is_null($id)) {
            return $returnId;
        }

        $this->currentSession = new Session($this->config->getName(), $id);

        return $returnId;
    }

    /**
     * Get and/or set the current session name
     */
    public function name(string $name = null)
    {
        $currentName = $this->config->getName();

        if ($name) {
            /**
             * The session name can't consist of digits only, at least one letter must be present.
             * Otherwise a new session id is generated every time.
             */
            if (ctype_digit($name)) {
                return false;
            }

            $this->config->setName($name);
        }

        return $currentName;
    }

    /**
     * Update the current session id with a newly generated one
     */
    public function regenerate_id(bool $delete_old_session = false): bool
    {
        $oldId = $this->currentSession->getId();
        $handler = $this->config->getSaveHandler();

        $newId = $this->create_id();
        $contents = $this->encode();

        if ($newId === false || $contents === false) {
            return false;
        }

        $isSaved = $handler->write($newId, $contents);

        if (!$isSaved) {
            return false;
        }

        $this->currentSession->open($newId);

        if ($delete_old_session) {
            return $handler->destroy($oldId);
        }

        return true;
    }

    /**
     * Session shutdown function
     */
    public function register_shutdown(): void
    {
        register_shutdown_function([$this, "write_close"]);
    }

    /**
     * Re-initialize session array with original values
     */
    public function reset(): bool
    {
        if (!$this->currentSession) {
            return false;
        }

        $handler = $this->config->getSaveHandler();

        if ($handler instanceof Handlers\SessionCasHandlerInterface) {
            list($contents, $token) = $handler->read_cas($this->currentSession->getId());
        } else {
            $contents = $handler->read($this->currentSession->getId());
        }

        if ($contents === false || !is_string($contents)) {
            return false;
        }

        $isDecoded = $this->decode($contents);

        if (!$isDecoded) {
            return false;
        }

        if ($handler instanceof Handlers\SessionCasHandlerInterface && isset($token)) {
            $this->currentSession->setCasToken($token);
        }

        return true;
    }

    /**
     * Get and/or set the current session save path
     */
    public function save_path(string $save_path = null)
    {
        if (is_null($save_path)) {
            return $this->config->getSavePath();
        }

        $this->config->setSavePath($save_path);
        return true;
    }

    /**
     * Sets user-level session storage functions
     */
    public function set_save_handler(
        \SessionHandlerInterface $save_handler,
        bool $register_shutdown = true
    ): bool {
        $this->config->setSaveHandler($save_handler);

        if ($register_shutdown) {
            $this->register_shutdown();
        }

        return true;
    }

    /**
     * Start new or resume existing session
     *
     * @return bool returns true if a session was successfully started, otherwise false
     */
    public function start(): bool
    {
        $handler = $this->config->getSaveHandler();

        $isOpen = $handler->open(
            $this->config->getSavePath(),
            $this->config->getName()
        );

        if (!$isOpen) {
            $handler->close();
            return false;
        }

        if ($this->config->getGcProbability() > 0) {
            $rnd = rand(1, 100);
            $probability = 100 * $this->config->getGcProbability() / $this->config->getGcDivisor();
            if ($rnd <= $probability) {
                $handler->gc($this->config->getGcMaxLifetime());
            }
        }

        if (
            !$this->currentSession
            || (
                $handler instanceof \SessionUpdateTimestampHandlerInterface
                && !$handler->validateId($this->currentSession->getId())
            )
        ) {
            $this->currentSession = new Session(
                $this->config->getName(),
                $this->sid->create_sid(),
                []
            );
            $handler->write($this->currentSession->getId(), $this->encode());
        }

        $id = $this->currentSession->getId();

        if ($handler instanceof Handlers\SessionCasHandlerInterface) {
            list($contents, $token) = $handler->read_cas($id);
        } else {
            $contents = $handler->read($id);
        }

        if ($contents === false) {
            if ($isOpen) {
                $handler->close();
            }
            return false;
        }

        if (is_string($contents)) {
            $isDecoded = $this->decode($contents);
    
            if (!$isDecoded) {
                $handler->destroy($id);
                $handler->close();
                $this->currentSession->close();
                return false;
            }
        }

        if ($handler instanceof Handlers\SessionCasHandlerInterface && isset($token)) {
            $this->currentSession->setCasToken($token);
        }

        /**
         * In addition to the normal set of configuration directives, a read_and_close option may
         * also be provided. If set to true, this will result in the session being closed
         * immediately after being read, thereby avoiding unnecessary locking if the session data
         * won't be changed.
         */
        if ($this->config->getReadAndClose()) {
            $this->currentSession->close();
            return $handler->close();
        }

        return true;
    }

    /**
     * Returns the current session status
     */
    public function status(): int
    {
        if (!$this->config->getSaveHandler()) {
            return \PHP_SESSION_DISABLED;
        }

        if (!$this->currentSession || !$this->currentSession->isInitialized()) {
            return \PHP_SESSION_NONE;
        }
        
        return \PHP_SESSION_ACTIVE;
    }

    /**
     * Free all session variables
     */
    public function unset(): bool
    {
        if (!$this->currentSession) {
            return false;
        }

        $keys = array_keys($this->currentSession->toArray());
        foreach ($keys as $key) {
            unset($this->currentSession->$key);
        }

        return true;
    }

    /**
     * Write session data and end session
     */
    public function write_close(): bool
    {
        if (!$this->currentSession) {
            return false;
        }

        $handler = $this->config->getSaveHandler();
        $id = $this->currentSession->getId();
        $contents = $this->encode();

        if ($contents === false) {
            $handler->destroy($id);
            $handler->close();
            $this->currentSession->close();
            throw new \RuntimeException("Data serialization failure");
            return false;
        }

        if (!$this->currentSession->isModified() && $this->config->getLazyWrite()) {
            $this->currentSession->close();
            return true;
        }

        if ($handler instanceof Handlers\SessionCasHandlerInterface) {
            $token = $this->currentSession->getCasToken();
            $success = $handler->write_cas($token, $id, $contents);
        } else {
            $success = $handler->write($id, $contents);
        }
        
        if ($success) {
            $this->currentSession->close();
            return true;
        }

        return false;
    }
}
