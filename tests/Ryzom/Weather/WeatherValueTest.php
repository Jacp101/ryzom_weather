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
 * Class WeatherValueTest
 */
class WeatherValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideWeatherFunction
     */
    public function testWeatherValue(CWeatherFunction $wf, $index, $weather, $localized, $condition)
    {
        $wv = new WeatherValue($index, $weather, $wf);

        $this->assertEquals($index, $wv->getIndex());
        $this->assertEquals($weather, $wv->getWeather(), '', 0.001);
        $this->assertEquals($localized, $wv->getLocalizedName());
        $this->assertEquals($condition, $wv->getWeatherDepositCondition());
    }

    public function provideWeatherFunction()
    {
        $wf6 = $this->getWeatherSetupWithSixElement();
        $wf3 = $this->getWeatherSetupWithThreeElement();
        $wf0 = $this->getWeatherSetupEmpty();
        return [
            // 6 setup weather function
            [$wf6, 0, 0.15, 'uiFair1', 'best'],
            [$wf6, 1, 0.20, 'uiFair2', 'good'],
            [$wf6, 2, 0.35, 'uiFair3', 'good'],
            [$wf6, 3, 0.55, 'uiFair4', 'bad'],
            [$wf6, 4, 0.75, 'uiFair5', 'bad'],
            [$wf6, 5, 0.85, 'uiSapThundery', 'worst'],
            // 3 setup weather function
            [$wf3, 0, 0.16, 'uiRainy1', 'best'],
            [$wf3, 0, 0.20, 'uiRainy1', 'good'],
            [$wf3, 1, 0.35, 'uiRainy2', 'good'],
            [$wf3, 1, 0.55, 'uiRainy2', 'bad'],
            [$wf3, 2, 0.75, 'uiThundery', 'bad'],
            [$wf3, 2, 0.85, 'uiThundery', 'worst'],
            // 0 setup weather function
            [$wf0, 0, 0, false, 'best'],
            [$wf0, 1, 0, false, 'best'],
            [$wf0, 2, 0, false, 'best'],
            [$wf0, 3, 0, false, 'best'],
            [$wf0, 4, 0, false, 'best'],
            [$wf0, 5, 0, false, 'best'],
        ];
    }

    /**
     * @return CWeatherFunction
     */
    private function getWeatherSetupWithSixElement()
    {
        $wfSetup = [
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
        return new CWeatherFunction($wfSetup);
    }

    /**
     * @return CWeatherFunction
     */
    private function getWeatherSetupWithThreeElement()
    {
        $wfSetup = [
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
        return new CWeatherFunction($wfSetup);
    }

    /**
     * @return CWeatherFunction
     */
    private function getWeatherSetupEmpty(){
        return new CWeatherFunction([]);
    }
}
