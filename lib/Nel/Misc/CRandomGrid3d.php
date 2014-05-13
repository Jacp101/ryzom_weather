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
 * Class CRandomGrid3d
 */
class CRandomGrid3d
{
    const NL3D_NOISE_LEVEL = 3;
    const NL3D_NOISE_GRID_SIZE_SHIFT = 5;
    const NL3D_NOISE_GRID_SIZE = 32; // 1 << NOISE_GRID_SIZE_SHIFT
    // NL3D_00255 = 1.0f / 255;
    const NL3D_OO255 = '0.00392156862745098039';

    /** @var array */
    private $texture3d;

    /* @var array */
    private $sizes;

    /** @var array */
    private $levelPhase;

    /**
     * @return CRandomGrid3d
     */
    static public function getInstance()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Load pre-calculated random number series from cached file.
     */
    private function __construct()
    {
        $file = __DIR__.'/../../../resources/randomgrid3d.serial';
        $data = unserialize(file_get_contents($file));

        $this->texture3d = $data['grid'];
        $this->sizes = $data['sizes'];
        $this->levelPhase = [];
        foreach ($data['phases'] as $idx => $xyz) {
            $this->levelPhase[$idx] = new CVector($xyz[0], $xyz[1], $xyz[2]);
        }
    }

    /**
     * @param int $level
     *
     * @return float
     */
    static public function getLevelSize($level)
    {
        return self::getInstance()->sizes[$level];
    }

    /**
     * @param int $level
     *
     * @return CVector
     */
    static public function getLevelPhase($level)
    {
        return self::getInstance()->levelPhase[$level];
    }

    /**
     * @param CVector $pos
     *
     * @return float
     */
    static public function evalBiLinear(CVector $pos)
    {
        $me = self::getInstance();

        $x = floor($pos->x);
        $y = floor($pos->y);
        $z = floor($pos->z);
        // index in texture
        $ux = $x & (self::NL3D_NOISE_GRID_SIZE - 1);
        $uy = $y & (self::NL3D_NOISE_GRID_SIZE - 1);
        $uz = $z & (self::NL3D_NOISE_GRID_SIZE - 1);
        $ux2 = ($x + 1) & (self::NL3D_NOISE_GRID_SIZE - 1);
        $uy2 = ($y + 1) & (self::NL3D_NOISE_GRID_SIZE - 1);
        $uz2 = ($z + 1) & (self::NL3D_NOISE_GRID_SIZE - 1);
        // delta
        $dx2 = $me->easeInEaseOut($pos->x - $x);
        $dy2 = $me->easeInEaseOut($pos->y - $y);
        $dz2 = $me->easeInEaseOut($pos->z - $z);
        $dx = 1 - $dx2;
        $dy = 1 - $dy2;
        $dz = 1 - $dz2;
        // TriLinear in texture3D
        $turb = 0;

        $dxdy = $dx * $dy;
        $turb += $me->lookup($ux, $uy, $uz) * $dxdy * $dz;
        $turb += $me->lookup($ux, $uy, $uz2) * $dxdy * $dz2;

        $dxdy2 = $dx * $dy2;
        $turb += $me->lookup($ux, $uy2, $uz) * $dxdy2 * $dz;
        $turb += $me->lookup($ux, $uy2, $uz2) * $dxdy2 * $dz2;

        $dx2dy = $dx2 * $dy;
        $turb += $me->lookup($ux2, $uy, $uz) * $dx2dy * $dz;
        $turb += $me->lookup($ux2, $uy, $uz2) * $dx2dy * $dz2;

        $dx2dy2 = $dx2 * $dy2;
        $turb += $me->lookup($ux2, $uy2, $uz) * $dx2dy2 * $dz;
        $turb += $me->lookup($ux2, $uy2, $uz2) * $dx2dy2 * $dz2;

        $return = $turb * self::NL3D_OO255;

        return $return;
    }

    /**
     * @param float $x
     *
     * @return float
     */
    private function easeInEaseOut($x)
    {
        $x2 = $x * $x;
        $x3 = $x2 * $x;
        $y = -2 * $x3 + 3 * $x2;

        return $y;
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return mixed
     */
    private function lookup($x, $y, $z)
    {
        $id = $x + ($y << self::NL3D_NOISE_GRID_SIZE_SHIFT) + ($z << (self::NL3D_NOISE_GRID_SIZE_SHIFT * 2));

        return $this->texture3d[$id];
    }
}
