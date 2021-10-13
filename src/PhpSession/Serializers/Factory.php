<?php

declare(strict_types=1);

namespace Compwright\PhpSession\Serializers;

class Factory
{
    public static function auto(string $handler = null): CallbackSerializer
    {
        switch ($handler) {
            case "json":
                return self::json();

            default:
            case "php":
            case "serialize":
            case "php_serialize":
                return self::php();
        }
    }

    public static function json(): CallbackSerializer
    {
        return new CallbackSerializer(
            function (array $contents): string {
                return \json_encode($contents, JSON_THROW_ON_ERROR);
            },
            function (string $contents): array {
                return \json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            }
        );
    }

    public static function php(): CallbackSerializer
    {
        return new CallbackSerializer('serialize', 'unserialize');
    }
}
