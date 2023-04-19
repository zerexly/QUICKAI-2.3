<?php

namespace EdSDK\FlmngrServer\lib\file\blurHash;

use InvalidArgumentException;

class Base83 {
    const ALPHABET = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
        'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f',
        'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't',
        'u', 'v', 'w', 'x', 'y', 'z', '#', '$', '%', '*', '+', ',', '-', '.',
        ':', ';', '=', '?', '@', '[', ']', '^', '_', '{', '|', '}', '~'
    ];

    const BASE = 83;

    public static function encode($value, $length) {
        if (floor($value / (self::BASE ** $length)) != 0) {
            throw new InvalidArgumentException('Specified length is too short to encode given value.');
        }

        $result = '';
        for ($i = 1; $i <= $length; $i++) {
            $digit   = floor($value / (self::BASE ** ($length - $i))) % self::BASE;
            $result .= self::ALPHABET[$digit];
        }
        return $result;
    }

    public static function decode($hash) {
        $result = 0;
        foreach (str_split($hash) as $char) {
            $result = $result * self::BASE + (int) array_search($char, self::ALPHABET, true);
        }
        return $result;
    }
}