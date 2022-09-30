<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;
use InvalidArgumentException;
use MatthiasMullie\Scrapbook\Adapters\Redis as RedisKeyValueStore;
use Redis;
use RuntimeException;

/**
 * Redis session store.
 */
class RedisHandler extends ScrapbookHandler
{
    private ?Redis $redis;

    public function __construct(Config $config)
    {
        $this->config = $config; // still required by SessionIdTrait
        $this->sid = new SessionId($config);

        if (!extension_loaded('redis')) {
            throw new RuntimeException('Missing redis extension');
        }
    }

    public function open($path, $name): bool
    {
        if (isset($this->redis)) {
            return false;
        }

        // Parse redis connection settings from save path
        $query = [];
        $config = parse_url($path);
        if ($config === false) {
            throw new InvalidArgumentException('Invalid $path');
        }
        if (!empty($config['query'])) {
            parse_str($config['query'], $query);
        }

        $redis = new Redis();

        if (empty($config['host'])) {
            throw new InvalidArgumentException('Missing host or socket in $path');
        }

        $port = isset($config['port']) && !is_null($config['port'])
            ? (int) $config['port']
            : null;

        if (!$redis->connect($config['host'], $port)) {
            unset($redis);
            return false;
        }

        $database = isset($query['database']) && !is_null($query['database'])
            ? (int) $query['database']
            : 0;

        if (!$redis->select($database)) {
            $redis->close();
            unset($redis);
            return false;
        }

        if (!$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE)) {
            return false;
        }

        $this->redis = $redis;
        $this->store = new RedisKeyValueStore($redis);
        return true;
    }

    public function close(): bool
    {
        $this->store = null;

        if (!isset($this->redis)) {
            return false;
        }

        $success = $this->redis->close();
        unset($this->redis);

        return $success;
    }
}
