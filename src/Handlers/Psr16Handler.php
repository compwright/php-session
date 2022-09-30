<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;
use Psr\SimpleCache\CacheInterface;
use SessionHandlerInterface;
use SessionIdInterface;
use SessionUpdateTimestampHandlerInterface;

/**
 * PSR-16 session store.
 */
class Psr16Handler implements
    SessionHandlerInterface,
    SessionUpdateTimestampHandlerInterface,
    SessionIdInterface,
    SessionLastModifiedTimestampHandlerInterface
{
    use SessionIdTrait;

    private SessionId $sid;

    private CacheInterface $store;

    private float $lastWriteTimestamp;

    public function __construct(Config $config, CacheInterface $store)
    {
        $this->store = $store;
        $this->sid = new SessionId($config);
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    /**
     * @param string $id
     * @return string|false
     */
    public function read($id)
    {
        if (!$this->store->has($id)) {
            return false;
        }

        return $this->store->get($id);
    }

    public function write($id, $data): bool
    {
        if (!is_string($data)) {
            return false;
        }

        $this->lastWriteTimestamp = microtime(true);

        return $this->store->set($id, $data);
    }

    public function validateId($id): bool
    {
        return !empty($id) && $this->store->has($id);
    }

    public function updateTimestamp($id, $data): bool
    {
        return $this->write($id, $data);
    }

    public function destroy($id): bool
    {
        if (!$this->store->has($id)) {
            return false;
        }

        return $this->store->delete($id);
    }

    public function gc($max_lifetime): bool
    {
        return true;
    }

    /**
     * @param string $id
     * @return float|false
     */
    public function getTimestamp($id)
    {
        return $this->lastWriteTimestamp ?? false;
    }
}
