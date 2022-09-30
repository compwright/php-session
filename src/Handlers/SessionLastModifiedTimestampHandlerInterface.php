<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Handlers;

interface SessionLastModifiedTimestampHandlerInterface
{
    /**
     * @param string $id
     * @return float|false
     */
    public function getTimestamp($id);
}
