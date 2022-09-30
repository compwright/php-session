<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Middleware;

use Compwright\PhpSession\Manager;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionBeforeMiddleware implements MiddlewareInterface
{
    private Manager $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function process(Request $request, Handler $handler): Response
    {
        $request = $request->withAttribute('sessionManager', $this->manager);
        return $handler->handle($request);
    }
}
