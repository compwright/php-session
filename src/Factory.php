<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use Psr\SimpleCache\CacheInterface;

class Factory
{
    public function configFromArray(array $settings): Config
    {
        $config = new Config();

        if (isset($settings['save_path'])) {
            $config->setSavePath($settings['save_path']);
        }

        if (isset($settings['save_handler'])) {
            $config->setSaveHandler($settings['save_handler']);
        }

        if (isset($settings['serialize_handler'])) {
            $config->setSerializeHandler(
                Serializers\Factory::auto($settings['serialize_handler'])
            );
        }

        if (isset($settings['name'])) {
            $config->setName($settings['name']);
        }

        if (isset($settings['cookie_lifetime'])) {
            $config->setCookieLifetime((int) $settings['cookie_lifetime']);
        }
        if (isset($settings['cookie_path'])) {
            $config->setCookiePath($settings['cookie_path']);
        }
        if (isset($settings['cookie_domain'])) {
            $config->setCookieDomain($settings['cookie_domain']);
        }
        if (isset($settings['cookie_secure'])) {
            $config->setCookieSecure((bool) $settings['cookie_secure']);
        }
        if (isset($settings['cookie_httponly'])) {
            $config->setCookieHttpOnly((bool) $settings['cookie_httponly']);
        }
        if (isset($settings['cookie_samesite'])) {
            $config->setCookieSameSite($settings['cookie_samesite']);
        }

        if (isset($settings['cache_limiter'])) {
            $config->setCacheLimiter($settings['cache_limiter']);
        }
        if (isset($settings['cache_expire'])) {
            $config->setCacheExpire((int) $settings['cache_expire']);
        }

        if (isset($settings['gc_probability'])) {
            $config->setGcProbability((int) $settings['gc_probability']);
        }

        if (isset($settings['gc_divisor'])) {
            $config->setGcDivisor((int) $settings['gc_divisor']);
        }

        if (isset($settings['gc_maxlifetime'])) {
            $config->setGcMaxLifetime((int) $settings['gc_maxlifetime']);
        }

        if (isset($settings['sid_length'])) {
            $config->setSidLength((int) $settings['sid_length']);
        }

        if (isset($settings['sid_bits_per_character'])) {
            $config->setSidBitsPerCharacter((int) $settings['sid_bits_per_character']);
        }

        if (isset($settings['lazy_write'])) {
            $config->setLazyWrite((bool) $settings['lazy_write']);
        }

        return $config;
    }

    public function configFromSystem(): Config
    {
        $config = new Config();

        $config->setSerializeHandler(
            Serializers\Factory::auto(ini_get('session.serialize_handler'))
        );

        $config->setName(ini_get('session.name'));

        $config->setCookieLifetime((int) ini_get('session.cookie_lifetime'));
        $config->setCookiePath(ini_get('session.cookie_path'));
        $config->setCookieDomain(ini_get('session.cookie_domain'));
        $config->setCookieSecure((bool) ini_get('session.cookie_secure'));
        $config->setCookieHttpOnly((bool) ini_get('session.cookie_httponly'));
        $config->setCookieSameSite(ini_get('session.cookie_samesite'));

        $config->setCacheLimiter(ini_get('session.cache_limiter'));
        $config->setCacheExpire((int) ini_get('session.cache_expire'));

        $config->setGcProbability((int) ini_get('session.gc_probability'));
        $config->setGcDivisor((int) ini_get('session.gc_divisor'));
        $config->setGcMaxLifetime((int) ini_get('session.gc_maxlifetime'));

        $config->setSidLength((int) ini_get('session.sid_length'));
        $config->setSidBitsPerCharacter((int) ini_get('session.sid_bits_per_character'));

        $config->setLazyWrite((bool) ini_get('session.lazy_write'));

        return $config;
    }

    public function psr16Session(CacheInterface $store, $arrayOrConfig = null): Manager
    {
        $config = is_array($arrayOrConfig)
            ? $this->configFromArray($arrayOrConfig)
            : $this->configFromSystem();

        $handler = new Handlers\Psr16Handler($config, $store);
        $config->setSaveHandler($handler);

        return new Manager($config);
    }
}
