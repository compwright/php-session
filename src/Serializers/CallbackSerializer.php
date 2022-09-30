<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

use Throwable;

class CallbackSerializer implements SerializerInterface
{
    private ?Throwable $lastError;

    /**
     * @var callable
     */
    private $serialize;

    /**
     * @var callable
     */
    private $unserialize;

    public function __construct(callable $serialize, callable $unserialize)
    {
        $this->serialize = $serialize;
        $this->unserialize = $unserialize;
    }

    public function serialize(array $contents): string
    {
        try {
            return call_user_func($this->serialize, $contents);
        } catch (Throwable $e) {
            $this->lastError = $e;
            throw $e;
        }
    }

    public function unserialize(string $contents): array
    {
        try {
            return call_user_func($this->unserialize, $contents);
        } catch (Throwable $e) {
            $this->lastError = $e;
            throw $e;
        }
    }

    public function getLastError(): ?Throwable
    {
        return $this->lastError ?? null;
    }
}
