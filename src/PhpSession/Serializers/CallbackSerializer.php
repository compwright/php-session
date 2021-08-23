<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class CallbackSerializer implements SerializerInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var callable
     */
    private $onSerialize;

    /**
     * @var callable
     */
    private $onUnserialize;

    public function __construct(SerializerInterface $serializer, callable $onSerialize, callable $onUnserialize)
    {
        $this->serializer = $serializer;
        $this->onSerialize = $onSerialize;
        $this->onUnserialize = $onUnserialize;
    }

    public function serialize(array $contents): string
    {
        return call_user_func($this->onSerialize, $this->serializer->serialize($contents));
    }

    public function unserialize(string $contents): array
    {
        return $this->serializer->unserialize(call_user_func($this->onUnserialize, $contents));
    }
}
