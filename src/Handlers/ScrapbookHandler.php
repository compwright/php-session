<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;
use MatthiasMullie\Scrapbook\KeyValueStore;
use SessionHandlerInterface;
use SessionIdInterface;
use SessionUpdateTimestampHandlerInterface;
use TypeError;

/**
 * KeyValueStore session store (matthiasmullie/scrapbook).
 */
class ScrapbookHandler implements
    SessionHandlerInterface,
    SessionUpdateTimestampHandlerInterface,
    SessionIdInterface,
    SessionCasHandlerInterface,
    SessionLastModifiedTimestampHandlerInterface
{
    use SessionIdTrait;

    protected Config $config;

    protected SessionId $sid;

    protected KeyValueStore $parentStore;

    protected KeyValueStore $store;

    protected bool $disableCollections;

    protected float $lastWriteTimestamp;

    public function __construct(
        Config $config,
        KeyValueStore $store,
        bool $disableCollections = false
    ) {
        $this->config = $config; // still required by SessionIdTrait
        $this->sid = new SessionId($config);
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

    public function read(string $id): string|false
    {
        $value = $this->store->get($id);
        if (is_string($value) || $value === false) {
            return $value;
        }
        throw new TypeError('Expected $value to be a string or false');
    }

    /**
     * @param string $id
     * @return array{mixed, string}|false
     */
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

        $this->lastWriteTimestamp = microtime(true);

        return $this->store->set($id, $data, $this->config->getGcMaxLifetime());
    }

    /**
     * @param mixed $token
     * @param string $id
     * @param mixed $data
     */
    public function write_cas($token, $id, $data): bool
    {
        $this->lastWriteTimestamp = microtime(true);

        return $this->store->cas($token, $id, $data, $this->config->getGcMaxLifetime());
    }

    public function validateId($id): bool
    {
        return !empty($id) && $this->store->get($id) !== false;
    }

    public function updateTimestamp($id, $data): bool
    {
        return $this->store->touch($id, $this->config->getGcMaxLifetime());
    }

    public function destroy($id): bool
    {
        return $this->store->delete($id);
    }

    public function gc(int $max_lifetime): int|false
    {
        return 0;
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
