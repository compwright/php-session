<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Iterator;
use Countable;
use ArrayAccess;
use RuntimeException;

class Session implements ArrayAccess, Iterator, Countable
{
    protected string $name;

    protected string $id;

    /**
     * @var mixed[]|null
     */
    protected ?array $contents = null;

    /**
     * @var int|float|string
     */
    protected $casToken;

    protected bool $writeable = true;

    protected bool $modified = false;

    /**
     * @param mixed[]|null $contents
     */
    public function __construct(string $name, ?string $id = null, array $contents = null)
    {
        $this->name = $name;

        if ($id) {
            $this->open($id, $contents);
        }
    }

    /**
     * @return mixed|null
     *
     * @throws RuntimeException if not initialized
     */
    public function &__get(string $name)
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        return $this->contents[$name];
    }

    public function __isset(string $name): bool
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return isset($this->contents[$name]);
    }

    /**
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        if (!$this->writeable) {
            throw new RuntimeException('Cannot alter session after it is closed');
        }

        $this->modified = true;
        $this->contents[$name] = $value;
    }

    public function __unset(string $name): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        if (!$this->writeable) {
            throw new RuntimeException('Cannot alter session after it is closed');
        }

        $this->modified = true;
        unset($this->contents[$name]);
    }

    public function offsetSet($name, $value): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        if (!$this->writeable) {
            throw new RuntimeException('Cannot alter session after it is closed');
        }

        $this->modified = true;
        $this->contents[$name] = $value;
    }

    public function offsetExists($name): bool
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return isset($this->contents[$name]);
    }

    public function offsetUnset($name): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        if (!$this->writeable) {
            throw new RuntimeException('Cannot alter session after it is closed');
        }

        $this->modified = true;
        unset($this->contents[$name]);
    }

    public function &offsetGet($name): mixed
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        return $this->contents[$name];
    }

    /**
     * @param ?array<string, mixed> $contents
     */
    public function open(string $id, array $contents = null): void
    {
        $this->id = $id;
        $this->modified = false;
        $this->writeable = true;
        if (!is_null($contents)) {
            $this->setContents($contents);
        }
    }

    public function getName(): string
    {
        return $this->name ?? '';
    }

    public function getId(): string
    {
        return $this->id ?? '';
    }

    /**
     * @return int|float|string
     */
    public function getCasToken()
    {
        return $this->casToken;
    }

    /**
     * @param int|float|string $token
     */
    public function setCasToken($token): void
    {
        $this->casToken = $token;
    }

    /**
     * @param array<string, mixed> $contents
     */
    public function setContents(array $contents): void
    {
        $this->contents = $contents;
    }

    public function isInitialized(): bool
    {
        return !is_null($this->contents);
    }

    public function isWriteable(): bool
    {
        return $this->writeable;
    }

    public function isModified(): bool
    {
        return $this->modified;
    }

    public function close(): void
    {
        $this->writeable = false;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RuntimeException if not initialized
     */
    public function toArray(): array
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return $this->contents ?? [];
    }

    public function rewind(): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        reset($this->contents);
    }

    public function current(): mixed
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        return current($this->contents);
    }

    public function key(): mixed
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        return key($this->contents);
    }

    public function next(): void
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        next($this->contents);
    }

    public function valid(): bool
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        // @phpstan-ignore-next-line
        return key($this->contents) !== null;
    }

    public function count(): int
    {
        return count($this->contents ?? []);
    }
}
