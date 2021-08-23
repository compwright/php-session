<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;

class FileHandler implements
    \SessionHandlerInterface,
    \SessionUpdateTimestampHandlerInterface,
    \SessionIdInterface,
    \Countable
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $savePath;

    public function __construct(Config $config)
    {
        $this->config = $config;
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

    public function read($id): bool
    {
        if (!$this->validateId($id)) {
            return false;
        }
        
        return (string) file_get_contents($this->getFilePath($id));
    }

    public function write($id, $data): bool
    {
        if (!$this->checkIdFormat($id)) {
            return false;
        }

        return !!file_put_contents($this->getFilePath($id), $data);
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

    public function create_sid(): string
    {
        $sid = new SessionId($this->config);

        do {
            $id = $sid->create_sid();
        } while ($this->validateId($id));

        unset($sid);
        return $id;
    }

    private function checkIdFormat($id): bool
    {
        return preg_match("/^[0-9A-Za-z-]+$/", $id) === 1;
    }

    public function validateId($id): bool
    {
        return (
            $this->checkIdFormat($id) 
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

    public function count(): int
    {
        return count(glob($this->getFilePath("*")));
    }
}
