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

namespace Ryzom;

/**
 * Class RyzomWeatherTest
 */
class RyzomWeatherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @param int $tick
     * @param int $weatherCycle
     *
     * @dataProvider tickWeatherCycleProvider
     */
    public function testGetWeatherCycle($tick, $weatherCycle)
    {
        $rw = new RyzomWeather();
        $rw->setServerTick($tick);
        $actual = (int)$rw->getWeatherCycle();

        $this->assertEquals($weatherCycle, $actual);
    }

    /**
     * @param int $gameTick
     * @param int $weatherCycle
     * @param string $continent
     * @param array $expected
     *
     * @dataProvider weatherProvider
     */
    public function testGetWeather($gameTick, $weatherCycle, $continent, $expected)
    {
        $rw = new RyzomWeather();
        $rw->setServerTick($gameTick);
        $wv = $rw->getWeather($continent);

        $this->assertEquals($weatherCycle, $rw->getWeatherCycle());
        $this->assertEquals($expected['value'], $wv->getWeather(), '', 0.0001);
        $this->assertEquals($expected['index'], $wv->getIndex());
        $this->assertEquals($expected['name'], $wv->getLocalizedName());
    }

    /**
     * @return array
     */
    public function tickWeatherCycleProvider()
    {
        return [
            [0, -488],
            [611659200, 112782], // day 14097, 18h
            [611659900, 112782], // day 14097, 19h
            [611678350, 112785], // day 14098, 03h
        ];
    }

    /**
     * @return array
     */
    public function weatherProvider()
    {
        // values from 32bit client, chopped to 5 digits
        return [
            [611725640, 112794, 'fyros', ['value' => 0.03750, 'name' => 'uiFair', 'index' => 0]], //06h
            [611729200, 112795, 'fyros', ['value' => 0.07916, 'name' => 'uiFair', 'index' => 0]], //09h
            [611733600, 112796, 'fyros', ['value' => 0.16249, 'name' => 'uiFair', 'index' => 0]], //12h
            [611739200, 112797, 'fyros', ['value' => 0.13333, 'name' => 'uiFair', 'index' => 0]], //15h
            [611744745, 112798, 'fyros', ['value' => 0.17500, 'name' => 'uiFair', 'index' => 1]], //18h
            [611749830, 112799, 'fyros', ['value' => 0.24166, 'name' => 'uiFair', 'index' => 1]], //21h
            [611755250, 112800, 'fyros', ['value' => 0.97619, 'name' => 'uiThundery', 'index' => 5]], //00h
            //
            [611760800, 112801, 'fyros', ['value' => 0.48333, 'name' => 'uiFair', 'index' => 2]], //03h
            [611766900, 112802, 'fyros', ['value' => 0.39166, 'name' => 'uiFair', 'index' => 2]], //06h
            [611771400, 112803, 'fyros', ['value' => 0.49166, 'name' => 'uiFair', 'index' => 2]], //09h
            [611777900, 112804, 'fyros', ['value' => 0.10833, 'name' => 'uiFair', 'index' => 0]], //12h
            [611782600, 112805, 'fyros', ['value' => 0.12500, 'name' => 'uiFair', 'index' => 0]], //15h
            [611787850, 112806, 'fyros', ['value' => 0.08750, 'name' => 'uiFair', 'index' => 0]], //18h
            [611793500, 112807, 'fyros', ['value' => 0.28333, 'name' => 'uiFair', 'index' => 1]], //21h
            [611799200, 112808, 'fyros', ['value' => 0.51666, 'name' => 'uiRainy', 'index' => 3]], //00h
            // 32bit, self compiled
            [612106200, 112865, 'tryker', ['value' => 0.63333, 'name' => 'uiRainy', 'index' => 3]],
            // TODO: prime roots weather, with uiSapThundery (index=5, 0.90 range)
        ];
    }
}
