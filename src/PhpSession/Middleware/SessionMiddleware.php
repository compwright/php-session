<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Middleware;

use Compwright\PhpSession\Manager;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public const EXPIRATION_KEY = "Compwright\PhpSession\ExpirationTimestamp";

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        /** @var Manager */
        $manager = $request->getAttribute("sessionManager");

        if (!$manager || !$manager instanceof Manager) {
            throw new \RuntimeException("Missing session manager");
        }

        if ($manager->status() === \PHP_SESSION_DISABLED) {
            throw new \RuntimeException(
                "Session is disabled, check if your save handler properly configured."
            );
        }

        // Start the session
        if ($manager->status() === \PHP_SESSION_NONE) {
            $manager->start();
        }

        $request = $request->withAttribute("session", $manager->getCurrentSession());

        // Rotate the session ID
        $interval = $manager->getConfig()->getRegenerateIdInterval();
        if ($interval > 0) {
            $expiry = time() + $interval;
            $session = $manager->getCurrentSession();
            $key = self::EXPIRATION_KEY;
            if (!isset($session->$key)) {
                $session->$key = $expiry;
            } elseif ($session->$key < time() || $session->$key > $expiry) {
                $manager->regenerate_id(true);
                $manager->getCurrentSession()->$key = $expiry;
            }
        }

        // Handle the request
        $response = $handler->handle($request);

        // Save and close the session
        if ($manager->status() === \PHP_SESSION_ACTIVE) {
            $isSaved = $manager->write_close();

            if (!$isSaved) {
                throw new \RuntimeException(
                    "Failed to save and close session, data may have been lost"
                );
            }
        }

        return $response;
    }
}
