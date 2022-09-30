<?php

declare(strict_types=1);

namespace Compwright\PhpSession;

use InvalidArgumentException;
use Psr\SimpleCache\CacheInterface;
use SessionHandlerInterface;

class Factory
{
    /**
     * @param array<string, mixed> $settings
     */
    public function configFromArray(array $settings): Config
    {
        $config = new Config();

        if (isset($settings['save_path'])) {
            if (!is_string($settings['save_path'])) {
                throw new InvalidArgumentException('save_path must be a string');
            }
            $config->setSavePath($settings['save_path']);
        }

        if (isset($settings['save_handler'])) {
            if (! ($settings['save_handler'] instanceof SessionHandlerInterface)) {
                throw new InvalidArgumentException('save_handler must implement SessionHandlerInterface');
            }
            $config->setSaveHandler($settings['save_handler']);
        }

        if (isset($settings['serialize_handler'])) {
            if (!is_string($settings['serialize_handler']) && !is_null($settings['serialize_handler'])) {
                throw new InvalidArgumentException('serialize_handler must be a string or null');
            }
            $config->setSerializeHandler(
                Serializers\Factory::auto($settings['serialize_handler'])
            );
        }

        if (isset($settings['name'])) {
            if (!is_string($settings['name'])) {
                throw new InvalidArgumentException('name must be a string');
            }
            $config->setName($settings['name']);
        }

        if (isset($settings['cookie_lifetime'])) {
            if (!is_int($settings['cookie_lifetime'])) {
                throw new InvalidArgumentException('cookie_lifetime must be an integer');
            }
            $config->setCookieLifetime($settings['cookie_lifetime']);
        }

        if (isset($settings['cookie_path'])) {
            if (!is_string($settings['cookie_path'])) {
                throw new InvalidArgumentException('cookie_path must be a string');
            }
            $config->setCookiePath($settings['cookie_path']);
        }

        if (isset($settings['cookie_domain'])) {
            if (!is_string($settings['cookie_domain'])) {
                throw new InvalidArgumentException('cookie_domain must be a string');
            }
            $config->setCookieDomain($settings['cookie_domain']);
        }

        if (isset($settings['cookie_secure'])) {
            if (!is_bool($settings['cookie_secure'])) {
                throw new InvalidArgumentException('cookie_secure must be a boolean');
            }
            $config->setCookieSecure($settings['cookie_secure']);
        }

        if (isset($settings['cookie_httponly'])) {
            if (!is_bool($settings['cookie_httponly'])) {
                throw new InvalidArgumentException('cookie_httponly must be a boolean');
            }
            $config->setCookieHttpOnly($settings['cookie_httponly']);
        }

        if (isset($settings['cookie_samesite'])) {
            if (!is_string($settings['cookie_samesite'])) {
                throw new InvalidArgumentException('cookie_samesite must be a string');
            }
            $config->setCookieSameSite($settings['cookie_samesite']);
        }

        if (isset($settings['cache_limiter'])) {
            if (!is_string($settings['cache_limiter'])) {
                throw new InvalidArgumentException('cache_limiter must be a string');
            }
            $config->setCacheLimiter($settings['cache_limiter']);
        }

        if (isset($settings['cache_expire'])) {
            if (!is_int($settings['cache_expire'])) {
                throw new InvalidArgumentException('cache_expire must be an integer');
            }
            $config->setCacheExpire($settings['cache_expire']);
        }

        if (isset($settings['gc_probability'])) {
            if (!is_int($settings['gc_probability'])) {
                throw new InvalidArgumentException('gc_probability must be an integer');
            }
            $config->setGcProbability($settings['gc_probability']);
        }

        if (isset($settings['gc_divisor'])) {
            if (!is_int($settings['gc_divisor'])) {
                throw new InvalidArgumentException('gc_divisor must be an integer');
            }
            $config->setGcDivisor($settings['gc_divisor']);
        }

        if (isset($settings['gc_maxlifetime'])) {
            if (!is_int($settings['gc_maxlifetime'])) {
                throw new InvalidArgumentException('gc_maxlifetime must be an integer');
            }
            $config->setGcMaxLifetime($settings['gc_maxlifetime']);
        }

        if (isset($settings['sid_length'])) {
            if (!is_int($settings['sid_length'])) {
                throw new InvalidArgumentException('sid_length must be an integer');
            }
            $config->setSidLength($settings['sid_length']);
        }

        if (isset($settings['sid_bits_per_character'])) {
            if (!is_int($settings['sid_bits_per_character'])) {
                throw new InvalidArgumentException('sid_bits_per_character must be an integer');
            }
            $config->setSidBitsPerCharacter($settings['sid_bits_per_character']);
        }

        if (isset($settings['lazy_write'])) {
            if (!is_bool($settings['lazy_write'])) {
                throw new InvalidArgumentException('lazy_write must be a boolean');
            }
            $config->setLazyWrite($settings['lazy_write']);
        }

        return $config;
    }

    public function configFromSystem(): Config
    {
        $config = new Config();

        $config->setSerializeHandler(
            Serializers\Factory::auto(ini_get('session.serialize_handler') ?: null)
        );

        if (ini_get('session.name') !== false) {
            $config->setName(ini_get('session.name'));
        }

        if (ini_get('session.cookie_lifetime') !== false) {
            $config->setCookieLifetime((int) ini_get('session.cookie_lifetime'));
        }

        if (ini_get('session.cookie_path') !== false) {
            $config->setCookiePath(ini_get('session.cookie_path'));
        }

        if (ini_get('session.cookie_domain') !== false) {
            $config->setCookieDomain(ini_get('session.cookie_domain'));
        }

        $config->setCookieSecure((bool) ini_get('session.cookie_secure'));
        $config->setCookieHttpOnly((bool) ini_get('session.cookie_httponly'));

        if (ini_get('session.cookie_samesite') !== false) {
            $config->setCookieSameSite(ini_get('session.cookie_samesite'));
        }

        if (ini_get('session.cache_limiter') !== false) {
            $config->setCacheLimiter(ini_get('session.cache_limiter'));
        }

        if (ini_get('session.cache_expire') !== false) {
            $config->setCacheExpire((int) ini_get('session.cache_expire'));
        }

        if (ini_get('session.gc_probability') !== false) {
            $config->setGcProbability((int) ini_get('session.gc_probability'));
        }

        if (ini_get('session.gc_divisor') !== false) {
            $config->setGcDivisor((int) ini_get('session.gc_divisor'));
        }

        if (ini_get('session.gc_maxlifetime') !== false) {
            $config->setGcMaxLifetime((int) ini_get('session.gc_maxlifetime'));
        }

        if (ini_get('session.sid_length') !== false) {
            $config->setSidLength((int) ini_get('session.sid_length'));
        }

        if (ini_get('session.sid_bits_per_character') !== false) {
            $config->setSidBitsPerCharacter((int) ini_get('session.sid_bits_per_character'));
        }

        $config->setLazyWrite((bool) ini_get('session.lazy_write'));

        return $config;
    }

    /**
     * @param array<string, mixed>|Config|null $arrayOrConfig
     */
    public function psr16Session(CacheInterface $store, $arrayOrConfig = null): Manager
    {
        if (is_array($arrayOrConfig)) {
            $config = $this->configFromArray($arrayOrConfig);
        } elseif (is_null($arrayOrConfig)) {
            $config = $this->configFromSystem();
        } elseif ($arrayOrConfig instanceof Config) {
            $config = $arrayOrConfig;
        } else {
            throw new InvalidArgumentException(
                '$arrayOrConfig must be an array, instance of Compwright\PhpSession\Config, or null'
            );
        }

        $handler = new Handlers\Psr16Handler($config, $store);
        $config->setSaveHandler($handler);

        return new Manager($config);
    }
}
