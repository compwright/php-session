<?php

namespace Compwright\PhpSession\BehaviorTest;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\Handlers\Psr16Handler;
use Compwright\PhpSession\Handlers\ScrapbookHandler;
use Compwright\PhpSession\Handlers\FileHandler;
use Compwright\PhpSession\Handlers\RedisHandler;
use RuntimeException;

trait GivenHandlerContextTrait
{
    /**
     * @Given session :handler stored at :location
     */
    public function sessionHandlerStoredAtLocation(string $handler, string $location): void
    {
        if ($handler !== 'redis') {
            $location = sys_get_temp_dir() . DIRECTORY_SEPARATOR . trim($location);
            if (!is_dir($location)) {
                mkdir($location, 0777, true);
            }
        }

        $this->config = new Config();

        $this->config->setSavePath($location);

        switch ($handler) {
            case 'kodus':
                $cache = new \Kodus\Cache\FileCache($location, $this->config->getGcMaxLifetime());
                $handler = new Psr16Handler($this->config, $cache);
                break;
            case 'scrapbook':
                $fs = new \League\Flysystem\Filesystem(
                    new \League\Flysystem\Local\LocalFilesystemAdapter($location, null, LOCK_EX)
                );
                $cache = new \MatthiasMullie\Scrapbook\Adapters\Flysystem($fs);
                $handler = new ScrapbookHandler($this->config, $cache);
                break;
            case 'redis':
                $this->config->setSavePath('tcp://localhost:6379?database=0');
                $handler = new RedisHandler($this->config);
                break;
            case 'file':
                $handler = new FileHandler($this->config);
                break;
            default:
                throw new RuntimeException('Not implemented: ' . $handler);
        }

        $this->config->setSaveHandler($handler);
    }
}
