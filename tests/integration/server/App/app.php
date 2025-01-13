<?php

declare(strict_types=1);

namespace App;

use DI\ContainerBuilder;
use Middlewares\AccessLog as AccessLogMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

use function Compwright\PhpSession\Frameworks\Slim\registerSessionMiddleware;

/**
 * @return App<ContainerInterface|null>
 */
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
    /** @var Routes\SessionRoutes $routes */
    $routes = $container->get(Routes\SessionRoutes::class);
    $app->get('/', fn(ServerRequestInterface $request, ResponseInterface $response) => $routes->readSession($request, $response));
    $app->post('/', fn(ServerRequestInterface $request, ResponseInterface $response) => $routes->writeSession($request, $response));

    return $app;
}
