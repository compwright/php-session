<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class Factory
{
    public static function auto(string $handler = null): SerializerInterface
    {
        switch ($handler) {
            case 'json':
                return self::json();

            default:
            case 'php':
            case 'serialize':
            case 'php_serialize':
                return self::php();
        }
    }

    public static function json(): JsonSerializer
    {
        return new JsonSerializer();
    }

    public static function php(): CallbackSerializer
    {
        return new CallbackSerializer('serialize', 'unserialize');
    }
}
