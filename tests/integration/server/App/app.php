<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use Middlewares\AccessLog as AccessLogMiddleware;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

use function Compwright\PhpSession\Frameworks\Slim\registerSessionMiddleware;

function app()
{
    $builder = new ContainerBuilder();
    $builder->addDefinitions(__DIR__ . '/config.php');
    $container = $builder->build();

    AppFactory::setContainer($container);
    $app = AppFactory::create();

    // Middleware
    $app->add(AccessLogMiddleware::class);
    registerSessionMiddleware($app);
    $app->addRoutingMiddleware(); // must come before ErrorMiddleware
    $app->addErrorMiddleware( // must come last
        true,  // display error details
        true,  // log errors
        false, // log error details
        $container->get(LoggerInterface::class)
    );

    // App routes
    $app->get('/', [Routes\SessionRoutes::class, 'readSession']);
    $app->post('/', [Routes\SessionRoutes::class, 'writeSession']);

    return $app;
}
