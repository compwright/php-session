<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use Middlewares\AccessLog as AccessLogMiddleware;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

use function Compwright\PhpSession\Frameworks\Slim\registerSessionMiddleware;

function app(): App
{
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/config.php');
    $container = $builder->build();

    AppFactory::setContainer($container);
    $app = AppFactory::create();

    /** @var LoggerInterface $logger */
    $logger = $container->get(LoggerInterface::class);

    // Middleware
    $app->add(AccessLogMiddleware::class);
    registerSessionMiddleware($app);
    $app->addRoutingMiddleware(); // must come before ErrorMiddleware
    $app->addErrorMiddleware( // must come last
        true,  // display error details
        true,  // log errors
        false, // log error details
        $logger
    );

    // App routes
    $app->get('/', [Routes\SessionRoutes::class, 'readSession']);
    $app->post('/', [Routes\SessionRoutes::class, 'writeSession']);

    return $app;
}
