<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Frameworks\Slim;

use Compwright\PhpSession\Middleware\SessionCacheControlMiddleware;
use Compwright\PhpSession\Middleware\SessionCookieMiddleware;
use Compwright\PhpSession\Middleware\SessionMiddleware;
use Slim\App;

function registerSessionMiddleware(App $app)
{
    // Slim middleware is executed in reverse order
    $app->add(SessionCacheControlMiddleware::class);
    $app->add(SessionMiddleware::class);
    $app->add(SessionCookieMiddleware::class);
    $app->add(SessionBeforeMiddleware::class);
}
