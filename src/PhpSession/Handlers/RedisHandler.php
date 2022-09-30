<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\SessionId;
use Exception;
use MatthiasMullie\Scrapbook\Adapters\Redis as RedisKeyValueStore;
use Redis;

/**
 * Redis session store.
 */
class RedisHandler extends ScrapbookHandler
{
    private ?Redis $redis = null;

    public function __construct(Config $config)
    {
        $this->config = $config; // still required by SessionIdTrait
        $this->sid = new SessionId($config);

        if (!extension_loaded('redis')) {
            throw new Exception('Missing redis extension');
        }
    }

    public function open($path, $name): bool
    {
        if ($this->redis) {
            return false;
        }

        $this->redis = null;
        $this->store = null;

        // Parse redis connection settings from save path
        $query = [];
        $config = parse_url($path);
        if (!empty($config['query'])) {
            parse_str($config['query'], $query);
        }
        
        $redis = new Redis();

        if (!$redis->connect($config['host'], (int) $config['port'])) {
            unset($redis);
            return false;
        }

        if (!$redis->select((int) $query['database'] ?? 0)) {
            $redis->close();
            unset($redis);
            return false;
        }

        $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);

        $this->redis = $redis;
        $this->store = new RedisKeyValueStore($redis);
        return true;
    }

    public function close(): bool
    {
        $this->store = null;

        if (!$this->redis) {
            return false;
        }

        $success = $this->redis->close();
        $this->redis = null;

        return $success;
    }
}
