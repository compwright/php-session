<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

use Throwable;
use TypeError;

class CallbackSerializer extends BaseSerializer
{
    /**
     * @var callable(array<string, mixed> contents): string
     */
    private $serialize;

    /**
     * @var callable(string $contents): array<string, mixed>
     */
    private $unserialize;

    /**
     * @param callable(array<string, mixed> $contents): string $serialize
     * @param callable(string $contents): array<string, mixed> $unserialize
     */
    public function __construct(callable $serialize, callable $unserialize)
    {
        $this->serialize = $serialize;
        $this->unserialize = $unserialize;
    }

    /**
     * @param array<string, mixed> $contents
     */
    public function serialize(array $contents): string
    {
        try {
            $encoded = call_user_func($this->serialize, $contents);
            if (is_string($encoded)) {
                return $encoded;
            }
            throw new TypeError('$serialize must return a string when invoked');
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
            $decoded = call_user_func($this->unserialize, $contents);
            if (is_array($decoded)) {
                /** @var array<string, mixed> */
                return $decoded;
            }
            // @phpstan-ignore-next-line
            throw new TypeError('$unserialize must return an array when invoked');
        } catch (Throwable $e) {
            $this->lastError = $e;
            throw $e;
        }
    }
}
