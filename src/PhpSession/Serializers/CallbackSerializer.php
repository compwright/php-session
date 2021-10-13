<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class CallbackSerializer implements SerializerInterface
{
    /**
     * @var \Exception
     */
    private $lastError;

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
        } catch (\Exception $e) {
            $this->lastError = $e;
            throw $e;
        }
    }

    public function unserialize(string $contents): array
    {
        try {
            return call_user_func($this->unserialize, $contents);
        } catch (\Exception $e) {
            $this->lastError = $e;
            throw $e;
        }
    }

    public function getLastError(): ?\Exception
    {
        return $this->lastError;
    }
}
