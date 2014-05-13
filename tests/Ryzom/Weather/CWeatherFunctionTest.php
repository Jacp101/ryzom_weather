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


class CWeatherFunctionTest extends \PHPUnit_Framework_TestCase
{

    public function testWeatherFunction()
    {
        $setup = [
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiFair1', 'setupname' => 'Fair1'],
                'weight' => 20,
            ],
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiFair2', 'setupname' => 'Fair2'],
                'weight' => 20,
            ],
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiFair3', 'setupname' => 'wind1'],
                'weight' => 20,
            ],
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiFair4', 'setupname' => 'humidity1'],
                'weight' => 15,
            ],
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiFair5', 'setupname' => 'humidity2'],
                'weight' => 15,
            ],
            [
                'setup' => ['lighting' => 1, 'localizedname' => 'uiSapThundery', 'setupname' => 'Thunderseve'],
                'weight' => 10,
            ],
        ];
        $wf = new CWeatherFunction($setup);

        $this->assertEquals(6, $wf->getNumWeatherSetups());
        $this->assertEquals(100, $wf->getWeatherSetupsTotalWeight());

        $this->assertEquals(20, $wf->getWeatherSetupWeight(0));
        $this->assertEquals($setup[0]['setup'], $wf->getWeatherSetup(0));

        $this->assertEquals(10, $wf->getWeatherSetupWeight(5));
        $this->assertEquals($setup[5]['setup'], $wf->getWeatherSetup(5));

        $this->assertEquals(false, $wf->getWeatherSetupWeight(6));
        $this->assertEquals(false, $wf->getWeatherSetup(6));
    }

    public function testThreeSetup()
    {
        $setup = [
            [
                'setup' => ['lighting' => 0.8, 'localizedname' => 'uiRainy1', 'setupname' => 'fair1'],
                'weight' => 50,
            ],
            [
                'setup' => ['lighting' => 0.8, 'localizedname' => 'uiRainy2', 'setupname' => 'fair1'],
                'weight' => 20,
            ],
            [
                'setup' => ['lighting' => 0.8, 'localizedname' => 'uiThundery', 'setupname' => 'storm'],
                'weight' => 30,
            ],
        ];
        $wf = new CWeatherFunction($setup);

        $this->assertEquals(3, $wf->getNumWeatherSetups());
        $this->assertEquals(100, $wf->getWeatherSetupsTotalWeight());

        $this->assertEquals(50, $wf->getWeatherSetupWeight(0));
        $this->assertEquals($setup[0]['setup'], $wf->getWeatherSetup(0));

        $this->assertEquals(30, $wf->getWeatherSetupWeight(2));
        $this->assertEquals($setup[2]['setup'], $wf->getWeatherSetup(2));

        $this->assertEquals(false, $wf->getWeatherSetupWeight(3));
        $this->assertEquals(false, $wf->getWeatherSetup(3));
    }

    /**
     * @return CWeatherFunction
     */
    private function getWeatherSetupEmpty()
    {
        return new CWeatherFunction([]);
    }
}
