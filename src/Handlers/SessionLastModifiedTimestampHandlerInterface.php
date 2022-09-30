<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Handlers;

interface SessionLastModifiedTimestampHandlerInterface
{
    public function getTimestamp($id);
}
