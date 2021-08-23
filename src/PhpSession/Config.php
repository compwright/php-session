<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Compwright\PhpSession\Serializers\PhpSerializer;
use Compwright\PhpSession\Serializers\SerializerInterface;

class Config
{
    /**
     * @var string
     */
    protected $save_path;

    public function setSavePath(string $save_path)
    {
        $this->save_path = $save_path;
    }

    public function getSavePath(): ?string
    {
        return $this->save_path;
    }

    /**
     * @var \SessionHandlerInterface
     */
    protected $save_handler;

    public function setSaveHandler(\SessionHandlerInterface $save_handler)
    {
        $this->save_handler = $save_handler;
    }

    public function getSaveHandler(): ?\SessionHandlerInterface
    {
        return $this->save_handler;
    }

    /**
     * @var string
     */
    protected $serialize_handler;

    public function setSerializeHandler(SerializerInterface $serialize_handler)
    {
        $this->serialize_handler = $serialize_handler;
    }

    public function getSerializeHandler(): ?SerializerInterface
    {
        return $this->serialize_handler ?? new PhpSerializer();
    }

    /**
     * @var string
     */
    protected $name = "PHPSESSID";

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @var int
     */
    protected $gc_probability = 1;

    public function setGcProbability(int $gc_probability)
    {
        $this->gc_probability = $gc_probability;
    }

    public function getGcProbability(): int
    {
        return $this->gc_probability;
    }

    /**
     * @var int
     */
    protected $gc_divisor = 100;

    public function setGcDivisor(int $gc_divisor)
    {
        $this->gc_divisor = $gc_divisor;
    }

    public function getGcDivisor(): int
    {
        return $this->gc_divisor;
    }

    /**
     * @var int
     */
    protected $gc_maxlifetime = 1440;

    public function setGcMaxLifetime(int $gc_maxlifetime)
    {
        $this->gc_maxlifetime = $gc_maxlifetime;
    }

    public function getGcMaxLifetime(): int
    {
        return $this->gc_maxlifetime;
    }

    /**
     * @var int
     */
    protected $sid_length = 32;

    public function setSidLength(int $sid_length)
    {
        if ($sid_length < 22 || $sid_length > 256) {
            throw new \InvalidArgumentException(
                "\$sid_length must be at least 22 and not greater than 256"
            );
        }

        $this->sid_length = $sid_length;
    }

    public function getSidLength(): int
    {
        return $this->sid_length;
    }

    /**
     * @var int
     */
    protected $sid_bits_per_character = 4;

    public function setSidBitsPerCharacter(int $sid_bits_per_character)
    {
        if ($sid_bits_per_character < 4 || $sid_bits_per_character > 6) {
            throw new \InvalidArgumentException(
                "\$sid_bits_per_character must be at least 4 and not greater than than 6"
            );
        }

        $this->sid_bits_per_character = $sid_bits_per_character;

        if ($sid_bits_per_character >= 5 && $this->sid_length < 26) {
            $this->setSidLength(26);
        }
    }

    public function getSidBitsPerCharacter(): int
    {
        return $this->sid_bits_per_character;
    }

    /**
     * @var bool
     */
    protected $lazy_write = true;

    public function setLazyWrite(bool $lazy_write)
    {
        $this->lazy_write = $lazy_write;
    }

    public function getLazyWrite(): bool
    {
        return $this->lazy_write;
    }

    /**
     * @var bool
     */
    protected $read_and_close = true;

    public function setReadAndClose(bool $read_and_close)
    {
        $this->read_and_close = $read_and_close;
    }

    public function getReadAndClose(): bool
    {
        return $this->read_and_close;
    }

    public function toArray(): array
    {
        $reflect = new \ReflectionClass($this);
        return array_reduce(
            $reflect->getProperties(\ReflectionProperty::IS_PROTECTED),
            function (array $array, \ReflectionProperty $prop) {
                $array[$prop->getName()] = $prop->getValue($this);
                return $array;
            },
            []
        );
    }
}
