<?php

declare(strict_types=1);

namespace App\Routes;

use Compwright\PhpSession\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $session = $request->getAttribute('session');
        $body = 'Hello, world: ' . $session->getId() . ', ' . ($session->counter ?? 0);
        $config = $this->config->toArray();
        $config['save_handler'] = get_class($config['save_handler']);
        $body .= "\n<pre>" . print_r($config, true);
        $response->getBody()->write($body);
        return $response;
    }

    public function writeSession(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        $session = $request->getAttribute('session');
        if (!isset($session->counter)) {
            $session->counter = 0;
        } else {
            $session->counter++;
        }
        $body = 'Hello, world: ' . $session->getId() . ', ' . $session->counter;
        $response->getBody()->write($body);
        return $response;
    }
}
