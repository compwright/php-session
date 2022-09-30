<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

use Throwable;

interface SerializerInterface
{
    /**
     * @param array<string, mixed> $contents
     */
    public function serialize(array $contents): string;

    /**
     * @return array<string, mixed>
     */
    public function unserialize(string $contents): array;

    public function getLastError(): ?Throwable;
}
