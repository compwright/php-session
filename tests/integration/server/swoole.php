<?php

declare(strict_types=1);

namespace App;

use Compwright\SwoolePsr7Compat\SwoolePsrRequestFactory;
use Compwright\SwoolePsr7Compat\SwoolePsrHandler;
use Nyholm\Psr7Server\ServerRequestCreator;
use Psr\Log\LoggerInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\UriFactory;
use Slim\Psr7\Factory\UploadedFileFactory;
use Slim\Psr7\Factory\StreamFactory;

require('vendor/autoload.php');

$app = app();

$logger = $app->getContainer()->get(LoggerInterface::class);

$handler = new SwoolePsrHandler(
    new SwoolePsrRequestFactory(
        new ServerRequestCreator(
            new ServerRequestFactory(),
            new UriFactory(),
            new UploadedFileFactory(),
            new StreamFactory()
        )
    )
);

\Swoole\Runtime::enableCoroutine(\SWOOLE_HOOK_ALL);

$server = new \Swoole\HTTP\Server('localhost', 8090);

$server->set([
    'reload_async' => true,
    'input_buffer_size' => '20M',
    'enable_coroutine' => true,
]);

$server->on('start', function ($server) use ($logger) {
    $logger->info(
        sprintf(
            'Swoole %s Server (http://%s:%s) started',
            swoole_version(),
            $server->host,
            $server->port
        )
    );
});

$server->on('request', $handler($app));

// Start server
$server->start();
