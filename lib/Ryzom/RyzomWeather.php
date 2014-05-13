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

use Ryzom\Weather\CPredictWeather;
use Ryzom\Weather\CWeatherFunction;
use Ryzom\Weather\WeatherValue;
use RyzomExtra\RyzomClock;

/**
 * Class RyzomWeather
 */
class RyzomWeather
{
    /** @var int */
    private $serverTick;

    /** @var string[] * */
    private $remap = [
        'lecarrefour' => 'nexus',
        'lepaysmalade' => 'zorai',
        'lesfalaises' => 'matis',
        // unknown continent 'lesilesvivantes',
    ];

    /** @var CWeatherFunction[] */
    private $weatherSetups;

    /**
     * init
     */
    public function __construct()
    {
        $this->predict = new CPredictWeather();
        $this->serverTick = 0;

        // TODO: move to own class
        // load per continent, per season weather setups/weights
        $this->weatherSetups = [];
        $array = include __DIR__.'/../../resources/weather-setups.php';
        foreach ($array as $continent => $seasons) {
            // remap some continent names to more familiar strings
            if (isset($this->remap[$continent])) {
                $continent = $this->remap[$continent];
            }

            foreach ($seasons as $season => $setups) {
                $this->weatherSetups[$continent][$season] = new CWeatherFunction($setups);
            }
        }
    }

    /**
     * @param int $tick
     */
    public function setServerTick($tick)
    {
        $this->serverTick = $tick;
    }

    /**
     * @return float
     */
    public function getHoursFromYearZero()
    {
        $day = ($this->serverTick / RyzomClock::RYZOM_DAY_IN_TICKS) - RyzomClock::RYZOM_START_SPRING;
        $hour = $day * RyzomClock::RYZOM_DAY_IN_HOUR;

        return $hour;
    }

    /**
     * Return weather cycle that is used to calculate weather value
     *
     * Weather cycle 0 is day 61, 00h
     *
     * @return int
     */
    public function getWeatherCycle()
    {
        return intval($this->getHoursFromYearZero() / 3);
    }

    /**
     * @param string $continent
     *
     * @return WeatherValue
     */
    public function getWeather($continent)
    {
        if (!isset($this->weatherSetups[$continent])) {
            return null;
        }

        $wfs = $this->weatherSetups[$continent];

        $weatherCycle = $this->getWeatherCycle();

        $season = $this->getSeasonIndex($weatherCycle);
        $seasonNames = ['spring', 'summer', 'autumn', 'winter'];
        $seasonName = $seasonNames[$season];

        $wf = $wfs[$seasonName];

        $weather = $this->predict->getCycleWeatherValue($weatherCycle, $wf);

        return $weather;
    }


    /**
     * Calculate season from weather cycle value
     * 0=spring
     *
     * @param int $wc
     *
     * @return int
     */
    private function getSeasonIndex($wc)
    {
        return RyzomClock::getSeasonFromRyzomDay($wc * 3 / 24);
    }
}
