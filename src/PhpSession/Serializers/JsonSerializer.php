<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class JsonSerializer implements SerializerInterface
{
    public function serialize(array $contents): string
    {
        return \json_encode($contents, JSON_THROW_ON_ERROR);
    }

    public function unserialize(string $contents): array
    {
        return \json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
    }
}
