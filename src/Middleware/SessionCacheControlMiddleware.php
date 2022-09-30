<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Middleware;

use Compwright\PhpSession\CacheControl;
use Compwright\PhpSession\Handlers\SessionLastModifiedTimestampHandlerInterface;
use Compwright\PhpSession\Manager;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

class SessionCacheControlMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);

        /** @var Manager */
        $manager = $request->getAttribute("sessionManager");

        if (!$manager || !$manager instanceof Manager) {
            throw new RuntimeException("Missing session manager");
        }

        if ($manager->status() === \PHP_SESSION_ACTIVE) {
            $config = $manager->getConfig();

            $handler = $config->getSaveHandler();

            $lastUpdated = $handler instanceof SessionLastModifiedTimestampHandlerInterface
                ? (int) ceil($handler->getTimestamp($manager->id()))
                : time();

            $cacheControl = CacheControl::createHeaders(
                $config->getCacheLimiter(),
                $config->getCacheExpire(),
                $lastUpdated ?: time()
            );

            foreach ($cacheControl as $header => $value) {
                $response = $response->withHeader($header, $value);
            }
        }

        return $response;
    }
}
