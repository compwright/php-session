<?php

declare(strict_types=1);

namespace App\Routes;

use Compwright\PhpSession\Config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SessionRoutes
{
    /** @var Config */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function readSession(Request $request, Response $response, $args)
    {
        $session = $request->getAttribute("session");
        $body = "Hello, world: " . $session->getId() . ", " . ($session->counter ?? 0);
        $config = $this->config->toArray();
        $config['save_handler'] = get_class($config['save_handler']);
        $body .= "\n<pre>" . print_r($config, true);
        $response->getBody()->write($body);
        return $response;
    }

    public function writeSession(Request $request, Response $response, $args)
    {
        $session = $request->getAttribute("session");
        if (!isset($session->counter)) {
            $session->counter = 0;
        } else {
            $session->counter++;
        }
        $body = "Hello, world: " . $session->getId() . ", " . $session->counter;
        $response->getBody()->write($body);
        return $response;
    }
}
