<?php

declare(strict_types=1);

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
        $desiredOutputLength = $this->config->getSidLength() - strlen($prefix);
        $bitsPerCharacter = $this->config->getSidBitsPerCharacter();

        $bytesNeeded = (int) ceil($desiredOutputLength * $bitsPerCharacter / 8);
        $randomInputBytes = random_bytes(max(1, $bytesNeeded));

        // The below is translated from function bin_to_readable in the PHP source
        // (ext/session/session.c)
        static $hexconvtab = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-';

        $out = '';

        $p = 0;
        $q = strlen($randomInputBytes);
        $w = 0;
        $have = 0;

        $mask = (1 << $bitsPerCharacter) - 1;

        $charsRemaining = $desiredOutputLength;
        while ($charsRemaining--) {
            if ($have < $bitsPerCharacter) {
                if ($p < $q) {
                    $byte = ord($randomInputBytes[$p++]);
                    $w |= ($byte << $have);
                    $have += 8;
                } else {
                    // Should never happen. Input must be large enough.
                    break;
                }
            }

            // consume $bitsPerCharacter bits
            $out .= $hexconvtab[$w & $mask];
            $w >>= $bitsPerCharacter;
            $have -= $bitsPerCharacter;
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
                return preg_match('/^[0-9a-f]+$/', $id) === 1;

            case 5:
                // 0123456789abcdefghijklmnopqrstuv
                return preg_match('/^[0-9a-v]+$/', $id) === 1;

            case 6:
                // 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ,-
                return preg_match('/^[0-9a-zA-Z,-]+$/', $id) === 1;
        }

        return false;
    }
}
