<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 session store.
 */
class Psr16Handler implements
    \SessionHandlerInterface,
    \SessionUpdateTimestampHandlerInterface,
    \SessionIdInterface
{
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var CacheInterface
     */
    private $store;

    use SessionIdTrait;

    public function __construct(Config $config, CacheInterface $store)
    {
        $this->config = $config; // still required by SessionIdTrait
        $this->store = $store;
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

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

        return $this->store->set($id, $data);
    }

    public function validateId($id): bool
    {
        return $this->store->has($id);
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
}
