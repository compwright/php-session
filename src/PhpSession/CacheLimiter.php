<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

class CacheLimiter
{
    /**
     * @var string
     */
    protected $limiter;

    /**
     * @var int
     */
    protected $expire;

    /**
     * @var int
     */
    protected $last_modified;

    public function __construct(string $limiter, int $expire = null, int $last_modified = null)
    {
        $this->limiter = $limiter;
        $this->expire = $expire;
        $this->last_modified = $last_modified;
    }

    public function __invoke(): array
    {
        switch ($this->limiter) {
            case "public":
                return [
                    "Expires"       => $this->getExpirationTimestamp(),
                    "Cache-Control" => "public, max-age=" . $this->expire,
                    "Last-Modified" => $this->getLastModifiedTimestamp(),
                ];
            case "private_no_expire":
                return [
                    "Cache-Control" => "private, max-age={$this->expire}, " .
                                       "pre-check={$this->expire}",
                    "Last-Modified" => $this->getLastModifiedTimestamp(),
                ];
            case "private":
                return [
                    "Expires"       => "Thu, 19 Nov 1981 08:52:00 GMT",
                    "Cache-Control" => "private, max-age={$this->expire}, " .
                                       "pre-check={$this->expire}",
                    "Last-Modified" => $this->getLastModifiedTimestamp(),
                ];
            case "nocache":
                return [
                    "Expires"       => "Thu, 19 Nov 1981 08:52:00 GMT",
                    "Cache-Control" => "no-store, no-cache, must-revalidate, " .
                                       "post-check=0, pre-check=0",
                    "Pragma"        => "no-cache",
                ];
            default:
                throw new \RuntimeException("Unsupported cache limiter: " . $this->limiter);
        }
    }

    protected function getExpirationTimestamp(): string
    {
        if (!is_numeric($this->expire)) {
            throw new \RuntimeException(
                "Cannot compute expiration timestamp without \$expire"
            );
        }
        return gmdate("D, d M Y H:i:s T", time() + $this->expire); // RFC2616
    }

    protected function getLastModifiedTimestamp(): string
    {
        if (!is_numeric($this->last_modified)) {
            throw new \RuntimeException(
                "Cannot compute last modified timestamp without \$last_modified"
            );
        }
        return gmdate("D, d M Y H:i:s T", $this->last_modified); // RFC2616
    }
}
