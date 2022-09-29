<?php

declare(strict_types=1);

// phpcs:ignoreFile PSR1.Methods.CamelCapsMethodName.NotCamelCaps

namespace Compwright\PhpSession;

use SessionIdInterface;

class SessionId implements SessionIdInterface
{
    protected Config $config;

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
        $prefix = $this->config->getSidPrefix();
        $desired_output_length = $this->config->getSidLength() - strlen($prefix);
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

        return $prefix . $out;
    }

    public function validate_sid(string $id): bool
    {
        if (strlen($id) !== $this->config->getSidLength()) {
            return false;
        }

        // Prefix might not validate under the rules for bits=4 or bits=5
        $prefix = $this->config->getSidPrefix();
        if ($prefix) {
            $id = substr($id, strlen($prefix));
        }
        
        switch ($this->config->getSidBitsPerCharacter()) {
            case 4:
                // 0123456789abcdef
                return preg_match("/^[0-9a-f]+$/", $id) === 1;

            case 5:
                // 0123456789abcdefghijklmnopqrstuv
                return preg_match("/^[0-9a-v]+$/", $id) === 1;

            case 6:
                // 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-
                return preg_match("/^[0-9a-zA-Z,-]+$/", $id) === 1;
        }

        return false;
    }
}
