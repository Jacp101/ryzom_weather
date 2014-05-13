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
 * Class CVector
 */
class CVector
{
    public $x;
    public $y;
    public $z;

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function __construct($x = 0, $y = 0, $z = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("CVector(%.5f, %.5f, %.5f)", $this->x, $this->y, $this->z);
    }

    /**
     * Multiply current vector with $m, return self
     *
     * @param float $m
     *
     * @return CVector
     */
    public function mul($m)
    {
        $this->x = $this->x * $m;
        $this->y = $this->y * $m;
        $this->z = $this->z * $m;

        return $this;
    }

    /**
     * Add vector to self and return new vector
     *
     * @param CVector $v
     *
     * @return CVector
     */
    public function add(CVector $v)
    {
        $ret = new CVector($this->x + $v->x, $this->y + $v->y, $this->z + $v->z);

        return $ret;
    }
}
