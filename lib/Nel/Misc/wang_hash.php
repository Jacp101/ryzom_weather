<?php
/*
 * RyzomWeather - https://github.com/nimetu/ryzom_weather
 * Copyright (c) 2014 Meelis Mägi <nimetu@gmail.com>
 *
 * This file is part of RyzomWeather.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nel\Misc;

/**
 * Calculate Wang hash for 64bit unsigned integer using GMP library
 * PHP only supports signed integers even with 64bit version
 *
 * See <code/nel/include/nel/misc/wang_hash.h> on https://bitbucket.org/ryzom/ryzomcore
 *
 * @param string $key
 *
 * @return string hash
 */
function wang_hash64($key) {
    // force $key to be base 10
    $key = gmp_init($key, 10);
    //$key = (~$key) + ($key << 21);
    $key = gmp_add(gmp_com($key), gmp_mul($key, 1 << 21));
    //$key = $key ^ ($key >> 24);
    $key = gmp_xor($key, gmp_div($key, 1 << 24));
    //$key = $key * 265;
    $key = gmp_mul($key, 265);
    //$key = $key ^ ($key >> 14);
    $key = gmp_xor($key, gmp_div($key, 1 << 14));
    //$key = $key * 21;
    $key = gmp_mul($key, 21);
    //$key = $key ^ ($key >> 28);
    $key = gmp_xor($key, gmp_div($key, 1 << 28));
    //$key = $key + ($key << 31);
    $key = gmp_add($key, gmp_mul($key, gmp_pow(2, 31)));
    // limit to 64bit
    $key = gmp_and($key, "0xFFFFFFFFFFFFFFFF");

    return gmp_strval($key, 10);
}

