<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class PhpSerializer implements SerializerInterface
{
    public function serialize(array $contents): string
    {
        return \serialize($contents);
    }

    public function unserialize(string $contents): array
    {
        return \unserialize($contents);
    }
}
