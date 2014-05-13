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

use RuntimeException;

/**
 * Generate random number series used in Ryzom's CRandomGrid3D
 *
 * This requires custom php_srand extension to get access real srand()/rand() functions
 */
class CRandomGridGenerator
{
    const RAND_MAX = 2147483647;

    /** @var int[] */
    private $texture3d;

    /** @var int[] */
    private $sizes;

    /** @var CVector[] */
    private $levelPhase;

    /**
     * Generate RandomGrid3D used by Ryzom
     */
    public function __construct()
    {
        $this->srand(0);

        $this->texture3d = array();

        // known series for verification
        $ryzomRand = array(
            43,
            30,
            195,
            67,
            226,
            231,
            162,
            199,
            249,
            230,
            197,
            189,
            15,
            247,
            79,
            10,
            3,
        );
        // .. and some between the buffer
        $ryzomRand[79] = 188;
        $ryzomRand[80] = 188;
        $ryzomRand[81] = 33;
        $ryzomRand[82] = 194;

        $fail = false;
        for ($z = 0; $z < CRandomGrid3d::NL3D_NOISE_GRID_SIZE; $z++) {
            for ($y = 0; $y < CRandomGrid3d::NL3D_NOISE_GRID_SIZE; $y++) {
                for ($x = 0; $x < CRandomGrid3d::NL3D_NOISE_GRID_SIZE; $x++) {
                    $id = $x + ($y << CRandomGrid3d::NL3D_NOISE_GRID_SIZE_SHIFT) + ($z << (CRandomGrid3d::NL3D_NOISE_GRID_SIZE_SHIFT * 2));
                    $v = $this->rand() >> 5;
                    $this->texture3d[$id] = $v & 0xFF;
                    if (isset($ryzomRand[$id]) && $this->texture3d[$id] !== $ryzomRand[$id]) {
                        $fail = true;
                    }
                }
            }
        }

        // test for known ryzom noise values
        if ($fail) {
            echo "FAIL:\nidx: expected got\n";
            foreach ($ryzomRand as $i => $v) {
                $got = $this->texture3d[$i];
                printf("% 3d: % 3d %s % 3d\n", $i, $got, $got != $v ? '!=' : '==', $v);
            }
            throw new RuntimeException("Failed to generate right number series");
        }

        $sum = 0;
        $this->sizes = [];
        for ($i = 0; $i < CRandomGrid3d::NL3D_NOISE_LEVEL; $i++) {
            $this->sizes[$i] = 1 / (1 << $i);
            $sum += $this->sizes[$i];
        }
        for ($i = 0; $i < CRandomGrid3d::NL3D_NOISE_LEVEL; $i++) {
            $this->sizes[$i] = $this->sizes[$i] / $sum;
        }

        // even tho 0 index is overwritten later, it advances rand() number
        $this->levelPhase = [];
        for ($i = 0; $i < CRandomGrid3d::NL3D_NOISE_LEVEL; $i++) {
            $this->levelPhase[$i] = new CVector(
                $this->frand(CRandomGrid3d::NL3D_NOISE_GRID_SIZE),
                $this->frand(CRandomGrid3d::NL3D_NOISE_GRID_SIZE),
                $this->frand(CRandomGrid3d::NL3D_NOISE_GRID_SIZE)
            );
        }
        // CVector::NULL
        $this->levelPhase[0] = new CVector(0, 0, 0);
    }

    /**
     * Serialize grid info to file
     *
     * @param string $file
     */
    public function dump($file)
    {
        // unroll CVector() so that we serialize array and not a class
        $phases = [];
        foreach ($this->levelPhase as $idx => $phase) {
            $phases[$idx] = [$phase->x, $phase->y, $phase->z];
        }
        $data = [
            'grid' => $this->texture3d,
            'sizes' => $this->sizes,
            'phases' => $phases,
        ];
        file_put_contents($file, serialize($data));
    }

    /**
     * Function from php_srand extension
     *
     * @param int $seed
     */
    private function srand($seed = 0)
    {
        if (function_exists('srand_init')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            srand_init($seed);
        } else {
            srand($seed);
        }
    }

    /**
     * Function from php_srand extension
     *
     * @return mixed
     */
    private function rand()
    {
        if (function_exists('srand_rand')) {
            /** @noinspection PhpUndefinedFunctionInspection */
            return srand_rand();
        } else {
            return rand();
        }
    }

    /**
     * @param int $mod
     *
     * @return float
     */
    private function frand($mod = 1)
    {
        return ($this->rand() / self::RAND_MAX) * $mod;
    }
}
