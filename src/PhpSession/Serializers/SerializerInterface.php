<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

interface SerializerInterface
{
    public function serialize(array $contents): string;

    public function unserialize(string $contents): array;

    public function getLastError(): ?\Exception;
}
