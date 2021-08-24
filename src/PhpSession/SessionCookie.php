<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;

class SessionCookie
{
    public static function create(
        string $name,
        string $id,
        int $maxAge = null,
        string $domain = null,
        string $path = null,
        bool $secure = false,
        bool $httpOnly = false,
        string $sameSite = null
    ): SetCookie {
        $expires = $maxAge
            ? gmdate('D, d M Y H:i:s T', time() + $maxAge)
            : null;

        $cookie = SetCookie::create($name, $id)
            ->withDomain($domain)
            ->withPath($path)
            ->withExpires($expires)
            ->withMaxAge($maxAge)
            ->withSecure($secure)
            ->withHttpOnly($httpOnly);

        if (!empty($sameSite)) {
            return $cookie->withSameSite(SameSite::fromString($sameSite));
        }

        return $cookie;
    }
}
