<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession\Handlers;

trait SessionIdTrait
{
    public function create_sid(): string
    {
        do {
            $id = $this->sid->create_sid();
        } while ($this->sid->validate_sid($id) && $this->validateId($id));

        return $id;
    }
}
