<?php

namespace EdSDK\FlmngrServer\lib\file\blurHash;

final class AC {

    public static function encode($value, $max_value) {
        $quant_r = static::quantise($value[0] / $max_value);
        $quant_g = static::quantise($value[1] / $max_value);
        $quant_b = static::quantise($value[2] / $max_value);
        return $quant_r * 19 * 19 + $quant_g * 19 + $quant_b;
    }

    public static function decode($value, $max_value) {
        $quant_r = floor($value / (19 * 19));
        $quant_g = floor($value / 19) % 19;
        $quant_b = $value % 19;

        return [
            static::signPow(($quant_r - 9) / 9, 2) * $max_value,
            static::signPow(($quant_g - 9) / 9, 2) * $max_value,
            static::signPow(($quant_b - 9) / 9, 2) * $max_value
        ];
    }

    private static function quantise($value) {
        return floor(max(0, min(18, floor(static::signPow($value, 0.5) * 9 + 9.5))));
    }

    private static function signPow($base, $exp) {
        $sign = $base > 0 ? 1 : ($base < 0 ? -1 : 0);
        return $sign * pow(abs($base), $exp);
    }
}