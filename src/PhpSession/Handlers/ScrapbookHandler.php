<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use MatthiasMullie\Scrapbook\KeyValueStore;

/**
 * KeyValueStore session store (matthiasmullie/scrapbook).
 */
class ScrapbookHandler implements
    \SessionHandlerInterface,
    \SessionUpdateTimestampHandlerInterface,
    \SessionIdInterface,
    SessionCasHandlerInterface
{
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var KeyValueStore
     */
    private $parentStore;

    /**
     * @var KeyValueStore
     */
    private $store;

    /**
     * @var bool
     */
    private $disableCollections;

    use SessionIdTrait;

    public function __construct(
        Config $config, 
        KeyValueStore $store, 
        bool $disableCollections = false
    ) {
        $this->config = $config; // still required by SessionIdTrait
        $this->parentStore = $store;
        $this->store = $store;
        $this->disableCollections = $disableCollections;
    }

    public function open($path, $name): bool
    {
        $this->store = $this->disableCollections
            ? $this->parentStore
            : $this->parentStore->getCollection($name);
        return true;
    }

    public function close(): bool
    {
        $this->store = $this->parentStore;
        return true;
    }

    public function read($id)
    {
        return $this->store->get($id);
    }

    public function read_cas($id)
    {
        $data = $this->store->get($id);

        if ($data === false) {
            return $data;
        }

        return [$data, serialize($data)];
    }

    public function write($id, $data): bool
    {
        if (!is_string($data)) {
            return false;
        }

        return $this->store->set($id, $data, $this->config->getGcMaxLifetime());
    }

    public function write_cas($token, $id, $data): bool
    {
        return $this->store->cas($token, $id, $data, $this->config->getGcMaxLifetime());
    }

    public function validateId($id): bool
    {
        return $this->store->get($id) !== false;
    }

    public function updateTimestamp($id, $data): bool
    {
        return $this->store->touch($id);
    }

    public function destroy($id): bool
    {
        return $this->store->delete($id);
    }

    public function gc($max_lifetime): bool
    {
        return true;
    }
}
