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

class CVectorTest extends \PHPUnit_Framework_TestCase
{
    public function testInit()
    {
        $vec = new CVector(1, 2, 3);
        $this->assertEquals(1, $vec->x);
        $this->assertEquals(2, $vec->y);
        $this->assertEquals(3, $vec->z);
    }

    public function testToString()
    {
        $vec = new CVector(1, 2, 3);
        $this->assertEquals('CVector(1.00000, 2.00000, 3.00000)', (string)$vec);
    }

    public function testMul()
    {
        $vec = new CVector(1, 2, 3);
        $vec->mul(2);
        $this->assertEquals(2, $vec->x);
        $this->assertEquals(4, $vec->y);
        $this->assertEquals(6, $vec->z);
    }

    public function testAdd()
    {
        $vec = new CVector(1, 1, 1);
        $vec2 = new CVector(5, 6, 7);
        $ret = $vec->add($vec2);

        $this->assertEquals(6, $ret->x);
        $this->assertEquals(7, $ret->y);
        $this->assertEquals(8, $ret->z);
    }
}
