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

namespace Ryzom\Weather;

/**
 * Class RyzomWeatherTest
 */
class CPredictWeatherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $cycle
     * @param float $expected
     * @param array $wfValues
     *
     * @dataProvider weatherProvider
     */
    public function testWeather($cycle, $expected, $wfValues)
    {
        $setups = [];
        foreach ($wfValues as $value) {
            $setups[] = ['weight' => $value, 'setup' => []];
        }
        $wf = new CWeatherFunction($setups);
        $pw = new CPredictWeather();

        $weather = $pw->getCycleWeatherValue($cycle, $wf);
        $this->assertEquals($expected, $weather->getWeather(), '', 0.0001);
    }

    /**
     * @return array
     */
    public function weatherProvider()
    {
        // TODO: more verified test cases with different weather setups
        $fyrosSpring = [40, 20, 20, 10, 3, 7];
        $fyrosSummer = [50, 30, 10, 3, 3, 4];
        $fyrosAutumn = [40, 20, 20, 10, 1, 9];
        $fyrosWinter = [30, 20, 15, 10, 10, 15];
        return [
            // php calculated, not accurate, just to test php code
            [0, 0.88095, $fyrosSpring],
            [0, 0.77777, $fyrosSummer],
            [1, 0.04583, $fyrosSpring],
            // verified
            [112794, 0.03750, $fyrosSpring],
        ];
    }
}
