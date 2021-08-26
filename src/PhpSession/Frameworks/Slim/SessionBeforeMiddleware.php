<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Frameworks\Slim;

use Compwright\PhpSession\Manager;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

class SessionBeforeMiddleware
{
    /**
     * @var Manager
     */
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $handler): Response
    {
        $request = $request->withAttribute("sessionManager", $this->manager);
        return $handler->handle($request);
    }
}
