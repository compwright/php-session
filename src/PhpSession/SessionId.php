<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession;

class SessionId implements \SessionIdInterface
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function __toString(): string
    {
        return $this->create_sid();
    }

    public function create_sid(): string
    {
        $desired_output_length = $this->config->getSidLength();
        $bits_per_character = $this->config->getSidBitsPerCharacter();

        $bytes_needed = ceil($desired_output_length * $bits_per_character / 8);
        $random_input_bytes = random_bytes((int) $bytes_needed);

        // The below is translated from function bin_to_readable in the PHP source
        // (ext/session/session.c)
        static $hexconvtab = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-';

        $out = '';

        $p = 0;
        $q = strlen($random_input_bytes);
        $w = 0;
        $have = 0;

        $mask = (1 << $bits_per_character) - 1;

        $chars_remaining = $desired_output_length;
        while ($chars_remaining--) {
            if ($have < $bits_per_character) {
                if ($p < $q) {
                    $byte = ord($random_input_bytes[$p++]);
                    $w |= ($byte << $have);
                    $have += 8;
                } else {
                    // Should never happen. Input must be large enough.
                    break;
                }
            }

            // consume $bits_per_character bits
            $out .= $hexconvtab[$w & $mask];
            $w >>= $bits_per_character;
            $have -= $bits_per_character;
        }

        return $out;
    }
}
