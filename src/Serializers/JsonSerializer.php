<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

use Throwable;

class JsonSerializer extends BaseSerializer
{
    /**
     * @param array<string, mixed> $contents
     */
    public function serialize(array $contents): string
    {
        try {
            return json_encode($contents, JSON_THROW_ON_ERROR) ?: '';
        } catch (Throwable $e) {
            $this->lastError = $e;
            throw $e;
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function unserialize(string $contents): array
    {
        try {
            /** @var array<string, mixed> */
            $decoded = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            return $decoded;
        } catch (Throwable $e) {
            $this->lastError = $e;
            throw $e;
        }
    }
}
