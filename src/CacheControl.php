<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use InvalidArgumentException;

class CacheControl
{
    private const EXPIRED = 'Thu, 19 Nov 1981 08:52:00 GMT';

    public static function createHeaders(
        string $limiter = 'nocache',
        int $maxAge = null,
        int $lastModified = null
    ): array {
        switch ($limiter) {
            case 'public':
                return [
                    'Expires'       => self::getExpirationTimestamp($maxAge),
                    'Cache-Control' => 'public, max-age=' . $maxAge,
                    'Last-Modified' => self::getLastModifiedTimestamp($lastModified),
                ];
            case 'private_no_expire':
                return [
                    'Cache-Control' => "private, max-age={$maxAge}, pre-check={$maxAge}",
                    'Last-Modified' => self::getLastModifiedTimestamp($lastModified),
                ];
            case 'private':
                return [
                    'Expires'       => self::EXPIRED,
                    'Cache-Control' => "private, max-age={$maxAge}, pre-check={$maxAge}",
                    'Last-Modified' => self::getLastModifiedTimestamp($lastModified),
                ];
            case 'nocache':
                return [
                    'Expires'       => self::EXPIRED,
                    'Cache-Control' => 'no-store, no-cache, must-revalidate, ' .
                                       'post-check=0, pre-check=0',
                    'Pragma'        => 'no-cache',
                ];
            default:
                throw new InvalidArgumentException('Invalid cache limiter: ' . $limiter);
        }
    }

    private static function getExpirationTimestamp(int $maxAge = null): string
    {
        if (is_null($maxAge)) {
            throw new InvalidArgumentException('$maxAge is required');
        }
        return gmdate('D, d M Y H:i:s T', time() + $maxAge); // RFC2616
    }

    private static function getLastModifiedTimestamp(int $lastModified = null): string
    {
        if (is_null($lastModified)) {
            throw new InvalidArgumentException('$lastModified is required');
        }
        return gmdate('D, d M Y H:i:s T', $lastModified); // RFC2616
    }
}
