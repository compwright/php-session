<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

class ConfigFactory
{
    public function createFromArray(array $settings): Config
    {
        $config = new Config();

        if (isset($settings["save_handler"])) {
            $config->setSaveHandler($settings["save_handler"]);
        }

        if (isset($settings["save_path"])) {
            $config->setSavePath($settings["save_path"]);
        }

        if (isset($settings["serialize_handler"])) {
            switch ($settings["serialize_handler"]) {
                case "serialize":
                case "php_serialize":
                case "Compwright\PhpSession\Serializers\PhpSerializer":
                    $config->setSerializeHandler(new Serializers\PhpSerializer());
                    break;
                case "json":
                case "Compwright\PhpSession\Serializers\JsonSerializer":
                    $config->setSerializeHandler(new Serializers\JsonSerializer());
                    break;
            }
        }

        if (isset($settings["name"])) {
            $config->setName($settings["name"]);
        }

        if (isset($settings["gc_probability"])) {
            $config->setGcProbability((int) $settings["gc_probability"]);
        }

        if (isset($settings["gc_divisor"])) {
            $config->setGcDivisor((int) $settings["gc_divisor"]);
        }

        if (isset($settings["gc_maxlifetime"])) {
            $config->setGcMaxLifetime((int) $settings["gc_maxlifetime"]);
        }

        if (isset($settings["sid_length"])) {
            $config->setSidLength((int) $settings["sid_length"]);
        }

        if (isset($settings["sid_bits_per_character"])) {
            $config->setSidBitsPerCharacter((int) $settings["sid_bits_per_character"]);
        }

        if (isset($settings["lazy_write"])) {
            $config->setLazyWrite((bool) $settings["lazy_write"]);
        }

        return $config;
    }

    public function createFromSystemConfig(): Config
    {
        $config = new Config();
        $config->setSaveHandler(new \SessionHandler());
        $config->setSavePath(ini_get("session.save_path"));

        switch (ini_get("session.serialize_handler")) {
            case "json":
            case "Compwright\PhpSession\Serializers\JsonSerializer":
                $config->setSerializeHandler(new Serializers\JsonSerializer());
                break;

            case "serialize":
            case "php_serialize":
            case "Compwright\PhpSession\Serializers\PhpSerializer":
            default:
                $config->setSerializeHandler(new Serializers\PhpSerializer());
                break;
        }

        $config->setName(ini_get("session.name"));
        $config->setGcProbability((int) ini_get("session.gc_probability"));
        $config->setGcDivisor((int) ini_get("session.gc_divisor"));
        $config->setGcMaxLifetime((int) ini_get("session.gc_maxlifetime"));
        $config->setSidLength((int) ini_get("session.sid_length"));
        $config->setSidBitsPerCharacter((int) ini_get("session.sid_bits_per_character"));
        $config->setLazyWrite((bool) ini_get("session.lazy_write"));

        return $config;
    }
}
