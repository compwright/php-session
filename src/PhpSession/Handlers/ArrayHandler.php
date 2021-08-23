<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;

/**
 * Array session store. This session store is non-locking and suitable only for testing.
 */
class ArrayHandler implements
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
     * @var array [string $data, array $meta = [string $id, int $last_modified, bool? $destroyed]]
     */
    private $store = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
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
        if (
            !array_key_exists($id, $this->store)
            || isset($this->store[$id]["meta"]["destroyed"])
        ) {
            return false;
        }

        return $this->store[$id]["data"];
    }

    public function write($id, $data): bool
    {
        if (!is_string($data)) {
            return false;
        }

        $this->store[$id] = [
            "data" => $data,
            "meta" => [
                "id" => $id,
                "last_modified" => time(),
            ],
        ];

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

    public function validateId($id): bool
    {
        return array_key_exists($id, $this->store);
    }

    public function updateTimestamp($id, $data): bool
    {
        if (
            !array_key_exists($id, $this->store)
            || isset($this->store[$id]["meta"]["destroyed"])
        ) {
            return false;
        }

        $this->store[$id]["meta"]["modified"] = time();

        return true;
    }

    public function destroy($id): bool
    {
        if (!isset($this->store[$id])) {
            return false;
        }

        $this->store[$id]["meta"]["destroyed"] = true;

        return true;
    }

    public function gc($max_lifetime): bool
    {
        $garbage = array_filter(
            $this->store,
            function ($store) use ($max_lifetime) {
                return (
                    isset($store["meta"]["destroyed"])
                    || $store["meta"]["last_modified"] < time() - $max_lifetime
                );
            }
        );

        if (count($garbage) === 0) {
            return false;
        }

        foreach ($garbage as $session) {
            unset($this->store[$session["meta"]["id"]]);
        }

        return true;
    }

    public function count()
    {
        return count($this->store);
    }
}
