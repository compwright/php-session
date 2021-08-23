<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Psr\SimpleCache\CacheInterface;

class Factory
{
    public function serializeHandler(string $handler): Serializers\SerializerInterface
    {
        switch ($handler) {
            case "json":
            case "Compwright\PhpSession\Serializers\JsonSerializer":
                return new Serializers\JsonSerializer();

            default:
            case "serialize":
            case "php_serialize":
            case "Compwright\PhpSession\Serializers\PhpSerializer":
                return new Serializers\PhpSerializer();
        }
    }

    public function configFromArray(array $settings): Config
    {
        $config = new Config();

        if (isset($settings["save_path"])) {
            $config->setSavePath($settings["save_path"]);
        }

        if (isset($settings["save_handler"])) {
            $config->setSaveHandler($settings["save_handler"]);
        }

        if (isset($settings["serialize_handler"])) {
            $config->setSerializeHandler($this->serializeHandler($settings["serialize_handler"]));
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

    public function configFromSystem(): Config
    {
        $config = new Config();

        $config->setSerializeHandler(
            $this->serializeHandler(ini_get("session.serialize_handler"))
        );

        $config->setName(ini_get("session.name"));

        $config->setGcProbability((int) ini_get("session.gc_probability"));
        $config->setGcDivisor((int) ini_get("session.gc_divisor"));
        $config->setGcMaxLifetime((int) ini_get("session.gc_maxlifetime"));

        $config->setSidLength((int) ini_get("session.sid_length"));
        $config->setSidBitsPerCharacter((int) ini_get("session.sid_bits_per_character"));

        $config->setLazyWrite((bool) ini_get("session.lazy_write"));

        return $config;
    }

    public function psr16Session(CacheInterface $store, $arrayOrConfig): Manager
    {
        if (!is_array($arrayOrConfig) && !($arrayOrConfig instanceof Config)) {
            throw new \InvalidArgumentException(
                "\$arrayOrConfig must be an array or an instance of Compwright\PhpSession\Config"
            );
        }

        $config = is_array($arrayOrConfig)
            ? $this->configFromArray($arrayOrConfig)
            : $this->configFromSystem();

        $handler = new Handlers\CacheHandler($config, $store);
        $config->setSaveHandler($handler);

        return new Manager($config);
    }
}
