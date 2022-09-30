<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

interface SessionCasHandlerInterface
{
    /**
     * @param string $id
     * @return mixed
     */
    public function read_cas($id);

    /**
     * @param mixed $token
     * @param string $id
     * @param mixed $data
     */
    public function write_cas($token, $id, $data): bool;
}
