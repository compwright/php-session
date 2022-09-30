<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Compwright\PhpSession\Serializers\Factory as SerializerFactory;
use Compwright\PhpSession\Serializers\SerializerInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;
use SessionHandlerInterface;

class Config
{
    protected string $save_path;

    public function setSavePath(string $save_path): self
    {
        $this->save_path = $save_path;
        return $this;
    }

    public function getSavePath(): ?string
    {
        return $this->save_path ?? null;
    }

    protected SessionHandlerInterface $save_handler;

    public function setSaveHandler(SessionHandlerInterface $save_handler): self
    {
        $this->save_handler = $save_handler;
        return $this;
    }

    public function getSaveHandler(): ?SessionHandlerInterface
    {
        return $this->save_handler ?? null;
    }

    protected SerializerInterface $serialize_handler;

    public function setSerializeHandler(SerializerInterface $serialize_handler): self
    {
        $this->serialize_handler = $serialize_handler;
        return $this;
    }

    public function getSerializeHandler(): SerializerInterface
    {
        return $this->serialize_handler ?? SerializerFactory::php();
    }

    protected string $name = 'PHPSESSID';

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected int $gc_probability = 1;

    public function setGcProbability(int $gc_probability): self
    {
        $this->gc_probability = $gc_probability;
        return $this;
    }

    public function getGcProbability(): int
    {
        return $this->gc_probability;
    }

    protected int $gc_divisor = 100;

    public function setGcDivisor(int $gc_divisor): self
    {
        $this->gc_divisor = $gc_divisor;
        return $this;
    }

    public function getGcDivisor(): int
    {
        return $this->gc_divisor;
    }

    protected int $gc_maxlifetime = 1440;

    public function setGcMaxLifetime(int $gc_maxlifetime): self
    {
        $this->gc_maxlifetime = $gc_maxlifetime;
        return $this;
    }

    public function getGcMaxLifetime(): int
    {
        return $this->gc_maxlifetime;
    }

    protected string $sid_prefix = '';

    public function setSidPrefix(string $sid_prefix): self
    {
        $this->sid_prefix = $sid_prefix;
        return $this;
    }

    public function getSidPrefix(): string
    {
        return $this->sid_prefix;
    }

    protected int $sid_length = 32;

    public function setSidLength(int $sid_length): self
    {
        if ($sid_length < 22 || $sid_length > 256) {
            throw new InvalidArgumentException(
                '$sid_length must be at least 22 and not greater than 256'
            );
        }

        $this->sid_length = $sid_length;
        return $this;
    }

    public function getSidLength(): int
    {
        return $this->sid_length;
    }

    protected int $sid_bits_per_character = 4;

    public function setSidBitsPerCharacter(int $sid_bits_per_character): self
    {
        if ($sid_bits_per_character < 4 || $sid_bits_per_character > 6) {
            throw new InvalidArgumentException(
                '$sid_bits_per_character must be at least 4 and not greater than than 6'
            );
        }

        $this->sid_bits_per_character = $sid_bits_per_character;

        if ($sid_bits_per_character >= 5 && $this->sid_length < 26) {
            $this->setSidLength(26);
        }

        return $this;
    }

    public function getSidBitsPerCharacter(): int
    {
        return $this->sid_bits_per_character;
    }

    protected bool $lazy_write = true;

    public function setLazyWrite(bool $lazy_write): self
    {
        $this->lazy_write = $lazy_write;
        return $this;
    }

    public function getLazyWrite(): bool
    {
        return $this->lazy_write;
    }

    protected bool $read_and_close = false;

    public function setReadAndClose(bool $read_and_close): self
    {
        $this->read_and_close = $read_and_close;
        return $this;
    }

    public function getReadAndClose(): bool
    {
        return $this->read_and_close;
    }

    protected int $cookie_lifetime = 0;

    public function setCookieLifetime(int $cookie_lifetime): self
    {
        $this->cookie_lifetime = $cookie_lifetime;
        return $this;
    }

    public function getCookieLifetime(): int
    {
        return $this->cookie_lifetime;
    }

    protected string $cookie_path = '/';

    public function setCookiePath(string $cookie_path): self
    {
        $this->cookie_path = $cookie_path;
        return $this;
    }

    public function getCookiePath(): string
    {
        return $this->cookie_path;
    }

    protected string $cookie_domain = '';

    public function setCookieDomain(string $cookie_domain): self
    {
        $this->cookie_domain = $cookie_domain;
        return $this;
    }

    public function getCookieDomain(): string
    {
        return $this->cookie_domain;
    }

    protected bool $cookie_secure = false;

    public function setCookieSecure(bool $cookie_secure): self
    {
        $this->cookie_secure = $cookie_secure;
        return $this;
    }

    public function getCookieSecure(): bool
    {
        return $this->cookie_secure;
    }

    protected bool $cookie_httponly = false;

    public function setCookieHttpOnly(bool $cookie_httponly): self
    {
        $this->cookie_httponly = $cookie_httponly;
        return $this;
    }

    public function getCookieHttpOnly(): bool
    {
        return $this->cookie_httponly;
    }

    protected string $cookie_samesite = '';

    public function setCookieSameSite(string $cookie_samesite): self
    {
        $this->cookie_samesite = $cookie_samesite;
        return $this;
    }

    public function getCookieSameSite(): string
    {
        return $this->cookie_samesite;
    }

    protected string $cache_limiter = 'nocache';

    public function setCacheLimiter(string $cache_limiter): self
    {
        $this->cache_limiter = $cache_limiter;
        return $this;
    }

    public function getCacheLimiter(): string
    {
        return $this->cache_limiter;
    }

    protected int $cache_expire = 180;

    public function setCacheExpire(int $cache_expire): self
    {
        $this->cache_expire = $cache_expire;
        return $this;
    }

    public function getCacheExpire(): int
    {
        return $this->cache_expire;
    }

    protected int $regenerate_id_interval = 0;

    public function setRegenerateIdInterval(int $regenerate_id_interval): self
    {
        $this->regenerate_id_interval = $regenerate_id_interval;
        return $this;
    }

    public function getRegenerateIdInterval(): int
    {
        return $this->regenerate_id_interval;
    }

    public function toArray(): array
    {
        $reflect = new ReflectionClass($this);
        return array_reduce(
            $reflect->getProperties(ReflectionProperty::IS_PROTECTED),
            function (array $array, ReflectionProperty $prop) {
                $prop->setAccessible(true);
                $array[$prop->getName()] = $prop->getValue($this);
                return $array;
            },
            []
        );
    }
}
