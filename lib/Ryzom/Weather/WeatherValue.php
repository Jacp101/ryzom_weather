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
 * Class RyzomWeatherValue
 */
class WeatherValue
{
    /** @var string[] */
    private $conditions = ['best', 'good', 'good', 'bad', 'bad', 'worst'];

    /** @var int */
    private $index;

    /** @var float */
    private $weather;

    /** @var CWeatherFunction */
    private $wf;

    /**
     * @param int $index
     * @param float $weather
     * @param CWeatherFunction $wf
     */
    public function __construct($index, $weather, CWeatherFunction $wf)
    {
        $this->index = $index;
        $this->weather = $weather;
        $this->wf = $wf;
    }

    /**
     * @return int
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return float
     */
    public function getWeather()
    {
        return $this->weather;
    }

    /**
     * Return weather setup value (eg, good, bad, worst)
     *
     * @return string
     */
    public function getWeatherDepositCondition()
    {
        // conditions: best=0, good=1, bad=2, worst=3
        //  best | good | good | bad  | bad | worst
        // 0 - 16.6 - 33.2 - 49.8 - 66.4 - 83 - 99.6
        $count = count($this->conditions);
        $wsValueIndex = min((int)($this->weather * $count), $count - 1);
        if ($wsValueIndex < 0) {
            return $this->conditions[0];
        }

        return $this->conditions[$wsValueIndex];
    }

    /**
     * Return current weather uxt translation key, eg. uiFair, uiRainy, uiThundery
     *
     * @return bool|string
     */
    public function getLocalizedName()
    {
        $setup = $this->wf->getWeatherSetup($this->index);
        if ($setup !== false) {
            return $setup['localizedname'];
        }
        return false;
    }
}
