<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

interface SessionCasHandlerInterface
{
    public function read_cas($id);

    public function write_cas($token, $id, $data): bool;
}
