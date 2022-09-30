<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Countable;
use RuntimeException;

class Session implements Countable
{
    protected string $name;

    protected string $id;

    protected ?array $contents = null;

    /**
     * @var int|float|string
     */
    protected $casToken;

    protected bool $writeable = true;

    protected bool $modified = false;

    public function __construct(string $name, string $id = null, array $contents = null)
    {
        $this->name = $name;

        if ($id) {
            $this->open($id, $contents);
        }
    }

    public function __get(string $name)
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return $this->contents[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return isset($this->contents[$name]);
    }

    public function __set(string $name, $value)
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

    public function __unset(string $name)
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

    public function open(string $id, array $contents = null)
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

    public function getCasToken()
    {
        return $this->casToken;
    }

    public function setCasToken($token)
    {
        $this->casToken = $token;
    }

    public function setContents(array $contents)
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

    public function close()
    {
        $this->writeable = false;
    }

    public function toArray(): array
    {
        if (!$this->isInitialized()) {
            throw new RuntimeException('Session not initialized');
        }

        return $this->contents;
    }

    public function count(): int
    {
        return count($this->contents ?? []);
    }
}
