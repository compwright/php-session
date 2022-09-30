<?php

declare(strict_types=1);

use Bramus\Monolog\Formatter\ColoredLineFormatter;
use Compwright\PhpSession\Config as SessionConfig;
use Compwright\PhpSession\Handlers\Psr16Handler as SessionSaveHandler;
use Kodus\Cache\FileCache;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

return [
    LoggerInterface::class => DI\factory(function () {
        $logger = new Logger('access');
        $logHandler = new StreamHandler(fopen('php://stdout', 'r+'));
        $logHandler->setFormatter(new ColoredLineFormatter());
        $logger->pushHandler($logHandler);
        return $logger;
    }),

    SessionConfig::class => DI\factory(function () {
        $config = (new SessionConfig())
            ->setRegenerateIdInterval(180)
            ->setCookieLifetime(3600)
            ->setCookiePath('/')
            ->setCookieSecure(false)
            ->setCookieHttpOnly(true)
            ->setCookieSameSite('strict')
            ->setCacheLimiter('nocache')
            ->setGcProbability(1)
            ->setGcDivisor(1)
            ->setGcMaxLifetime(7200)
            ->setSidLength(48)
            ->setSidBitsPerCharacter(5)
            ->setLazyWrite(true);

        $config->setSavePath(sys_get_temp_dir() . DIRECTORY_SEPARATOR . $config->getName());

        $config->setSaveHandler(
            new SessionSaveHandler($config, new FileCache(
                $config->getSavePath(),
                $config->getGcMaxLifetime()
            ))
        );

        return $config;
    })
];
