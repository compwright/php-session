<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Middleware;

use Compwright\PhpSession\Manager;
use Compwright\PhpSession\SessionCookie;
use Dflydev\FigCookies\FigResponseCookies;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class SessionCookieMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $manager = $request->getAttribute("sessionManager");

        if (!$manager || !$manager instanceof Manager) {
            throw new RuntimeException("Missing session manager");
        }

        // Read the session cookie
        $cookies = $request->getCookieParams();
        $sid = $cookies[$manager->name()] ?? "";
        $manager->id($sid);

        // Handle the request
        $response = $handler->handle($request);

        // If the session ID changed, write a new session cookie
        if ($manager->id() !== $sid) {
            $config = $manager->getConfig();
            return FigResponseCookies::set($response, SessionCookie::create(
                $manager->name(),
                $manager->id(),
                $config->getCookieLifetime(),
                $config->getCookieDomain(),
                $config->getCookiePath(),
                $config->getCookieSecure(),
                $config->getCookieHttpOnly(),
                $config->getCookieSameSite()
            ));
        }

        return $response;
    }
}
