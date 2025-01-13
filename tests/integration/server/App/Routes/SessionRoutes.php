<?php

declare(strict_types=1);

namespace App\Routes;

use Compwright\PhpSession\Config;
use Compwright\PhpSession\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use SessionHandlerInterface;

class SessionRoutes
{
    private Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function readSession(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        /** @var Session $session */
        $session = $request->getAttribute('session');

        /** @var int $count */
        $count = $session->counter ?? 0;
        $body = 'Hello, world: ' . $session->getId() . ', ' . strval($count);

        $config = $this->config->toArray();
        /** @var SessionHandlerInterface $handler */
        $handler = $config['save_handler'];
        $config['save_handler'] = get_class($handler);

        $body .= "\n<pre>" . print_r($config, true);

        $response->getBody()->write($body);

        return $response;
    }

    public function writeSession(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        /** @var Session $session */
        $session = $request->getAttribute('session');
        if (!isset($session->counter)) {
            $session->counter = 0;
        } else {
            /** @var int $count */
            $count = $session->counter;
            $session->counter = ++$count;
        }
        $body = 'Hello, world: ' . $session->getId() . ', ' . strval($count ?? 0);
        $response->getBody()->write($body);
        return $response;
    }
}
