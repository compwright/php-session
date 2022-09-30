<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

class ExtraConfig
{
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

    protected bool $cookie_samesite = false;

    public function setCookieSameSite(bool $cookie_samesite): self
    {
        $this->cookie_samesite = $cookie_samesite;
        return $this;
    }

    public function getCookieSameSite(): bool
    {
        return $this->cookie_samesite;
    }

    protected bool $use_cookies = true;

    public function setUseCookies(bool $use_cookies): self
    {
        $this->use_cookies = $use_cookies;
        return $this;
    }

    public function getUseCookies(): bool
    {
        return $this->use_cookies;
    }

    protected bool $use_only_cookies = true;

    public function setUseOnlyCookies(bool $use_only_cookies): self
    {
        $this->use_only_cookies = $use_only_cookies;
        return $this;
    }

    public function getUseOnlyCookies(): bool
    {
        return $this->use_only_cookies;
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
}
