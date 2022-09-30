<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

class ExtraConfig
{
    protected int $cookie_lifetime = 0;

    public function setCookieLifetime(int $cookie_lifetime)
    {
        $this->cookie_lifetime = $cookie_lifetime;
    }

    public function getCookieLifetime(): int
    {
        return $this->cookie_lifetime;
    }

    protected string $cookie_path = "/";

    public function setCookiePath(string $cookie_path)
    {
        $this->cookie_path = $cookie_path;
    }

    public function getCookiePath(): string
    {
        return $this->cookie_path;
    }

    protected string $cookie_domain = "";

    public function setCookieDomain(string $cookie_domain)
    {
        $this->cookie_domain = $cookie_domain;
    }

    public function getCookieDomain(): string
    {
        return $this->cookie_domain;
    }

    protected bool $cookie_secure = false;

    public function setCookieSecure(bool $cookie_secure)
    {
        $this->cookie_secure = $cookie_secure;
    }

    public function getCookieSecure(): bool
    {
        return $this->cookie_secure;
    }

    protected bool $cookie_httponly = false;

    public function setCookieHttpOnly(bool $cookie_httponly)
    {
        $this->cookie_httponly = $cookie_httponly;
    }

    public function getCookieHttpOnly(): bool
    {
        return $this->cookie_httponly;
    }

    protected bool $cookie_samesite = false;

    public function setCookieSameSite(bool $cookie_samesite)
    {
        $this->cookie_samesite = $cookie_samesite;
    }

    public function getCookieSameSite(): bool
    {
        return $this->cookie_samesite;
    }

    protected bool $use_cookies = true;

    public function setUseCookies(bool $use_cookies)
    {
        $this->use_cookies = $use_cookies;
    }

    public function getUseCookies(): bool
    {
        return $this->use_cookies;
    }

    protected bool $use_only_cookies = true;

    public function setUseOnlyCookies(bool $use_only_cookies)
    {
        $this->use_only_cookies = $use_only_cookies;
    }

    public function getUseOnlyCookies(): bool
    {
        return $this->use_only_cookies;
    }

    protected string $cache_limiter = "nocache";

    public function setCacheLimiter(string $cache_limiter)
    {
        $this->cache_limiter = $cache_limiter;
    }

    public function getCacheLimiter(): string
    {
        return $this->cache_limiter;
    }

    protected int $cache_expire = 180;

    public function setCacheExpire(int $cache_expire)
    {
        $this->cache_expire = $cache_expire;
    }

    public function getCacheExpire(): int
    {
        return $this->cache_expire;
    }
}
