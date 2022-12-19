<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;
use Countable;
use SessionHandlerInterface;
use SessionIdInterface;
use SessionUpdateTimestampHandlerInterface;
use TypeError;

/**
 * Array session store. This session store does not actually persist data and is meant only for testing.
 */
class ArrayHandler implements
    SessionHandlerInterface,
    SessionUpdateTimestampHandlerInterface,
    SessionIdInterface,
    Countable,
    SessionCasHandlerInterface,
    SessionLastModifiedTimestampHandlerInterface
{
    use SessionIdTrait;

    private SessionId $sid;

    /**
     * @var array<string, array{data: mixed, meta: array{id: string, last_modified: float, destroyed?: bool}}>
     */
    private array $store;

    /**
     * @param array<string, array{data: mixed, meta: array{id: string, last_modified: float, destroyed?: bool}}> $store
     */
    public function __construct(Config $config, array $store = [])
    {
        $this->sid = new SessionId($config);
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

    public function read(string $id): string|false
    {
        if (
            !array_key_exists($id, $this->store)
            || isset($this->store[$id]['meta']['destroyed'])
        ) {
            return false;
        }

        $value = $this->store[$id]['data'];
        if (is_string($value) || $value === false) {
            return $value;
        }
        throw new TypeError('Expected $value to be a string or false');
    }

    /**
     * @param string $id
     * @return array{mixed, float}|false
     */
    public function read_cas($id)
    {
        $data = $this->read($id);

        if ($data === false) {
            return false;
        }

        return [$data, $this->store[$id]['meta']['last_modified']];
    }

    public function write($id, $data): bool
    {
        if (!is_string($data)) {
            return false;
        }

        $this->store[$id] = [
            'data' => $data,
            'meta' => [
                'id' => $id,
                'last_modified' => microtime(true),
            ],
        ];

        return true;
    }

    /**
     * @param float $token
     * @param string $id
     * @param string $data
     */
    public function write_cas($token, $id, $data): bool
    {
        if (
            array_key_exists($id, $this->store)
            && $token !== $this->store[$id]['meta']['last_modified']
        ) {
            return false;
        }

        return $this->write($id, $data);
    }

    public function validateId($id): bool
    {
        return (
            !empty($id)
            && array_key_exists($id, $this->store)
            && !isset($this->store[$id]['meta']['destroyed'])
        );
    }

    public function updateTimestamp($id, $data): bool
    {
        if (
            !array_key_exists($id, $this->store)
            || isset($this->store[$id]['meta']['destroyed'])
        ) {
            return false;
        }

        $this->store[$id]['meta']['last_modified'] = microtime(true);

        return true;
    }

    /**
     * @param string $id
     * @return float|false
     */
    public function getTimestamp($id)
    {
        if (
            !array_key_exists($id, $this->store)
            || isset($this->store[$id]['meta']['destroyed'])
        ) {
            return false;
        }

        return $this->store[$id]['meta']['last_modified'];
    }

    public function destroy($id): bool
    {
        if (!array_key_exists($id, $this->store)) {
            return false;
        }

        $this->store[$id]['meta']['destroyed'] = true;

        return true;
    }

    public function gc($max_lifetime): int|false
    {
        $garbage = array_filter(
            $this->store,
            function ($store) use ($max_lifetime) {
                return (
                    isset($store['meta']['destroyed'])
                    || $store['meta']['last_modified'] < microtime(true) - $max_lifetime
                );
            }
        );

        if (count($garbage) === 0) {
            return false;
        }

        foreach ($garbage as $session) {
            unset($this->store[$session['meta']['id']]);
        }

        return count($garbage);
    }

    public function count(): int
    {
        return count($this->store);
    }

    /**
     * @return array<string, array{data: mixed, meta: array{id: string, last_modified: float, destroyed?: bool}}>
     */
    public function toArray(): array
    {
        return $this->store;
    }
}
