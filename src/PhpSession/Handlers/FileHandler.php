<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;

/**
 * File-based session store. This session store is non-locking and suitable only for testing.
 */
class FileHandler implements
    \SessionHandlerInterface,
    \SessionUpdateTimestampHandlerInterface,
    \SessionIdInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SessionId
     */
    private $sid;

    /**
     * @var string
     */
    private $savePath;

    use SessionIdTrait;

    public function __construct(Config $config)
    {
        // required for SessionIdTrait
        $this->config = $config;
        $this->sid = new SessionId($config);
    }

    private function getFilePath(string $id): string
    {
        return $this->savePath . DIRECTORY_SEPARATOR . "sess_" . $id;
    }

    public function open($savePath, $sessionName): bool
    {
        $this->savePath = $savePath;

        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read($id)
    {
        if (!$this->validateId($id)) {
            return false;
        }
        
        return (string) file_get_contents($this->getFilePath($id));
    }

    public function write($id, $data): bool
    {
        if (!$this->sid->validate_sid($id)) {
            return false;
        }

        return file_put_contents($this->getFilePath($id), $data) !== false;
    }

    public function destroy($id): bool
    {
        if (!$this->validateId($id)) {
            return false;
        }

        return unlink($this->getFilePath($id));
    }

    public function gc($maxlifetime): bool
    {
        foreach (glob($this->getFilePath("*")) as $file) {
            if (file_exists($file) && time() > filemtime($file) + $maxlifetime) {
                unlink($file);
            }
        }

        return true;
    }

    public function validateId($id): bool
    {
        return (
            $this->sid->validate_sid($id) 
            && file_exists($this->getFilePath($id))
        );
    }

    public function updateTimestamp($id, $data): bool
    {
        if (!$this->validateId($id)) {
            return false;
        }

        touch($this->getFilePath($id));

        return true;
    }
}
