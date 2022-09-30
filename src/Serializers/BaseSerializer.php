<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

use Throwable;

abstract class BaseSerializer implements SerializerInterface
{
    protected ?Throwable $lastError;

    /**
     * @param array<string, mixed> $contents
     */
    abstract public function serialize(array $contents): string;

    /**
     * @return array<string, mixed>
     */
    abstract public function unserialize(string $contents): array;

    public function getLastError(): ?Throwable
    {
        return $this->lastError ?? null;
    }
}
