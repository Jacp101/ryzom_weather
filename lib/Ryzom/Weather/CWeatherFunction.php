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
 * Holds single season weather weight values
 */
class CWeatherFunction
{
    /** @var int[] */
    private $weights;

    /** @var string[] */
    private $setups;

    /** @var int */
    private $nbWeights;

    /** @var int */
    private $sumWeights;

    /**
     * @param array $setups
     */
    public function __construct(array $setups = [])
    {
        $this->weights = [];
        $this->setups = [];

        foreach ($setups as $array) {
            $this->weights[] = $array['weight'];
            $this->setups[] = $array['setup'];
        }

        $this->nbWeights = count($this->weights);
        $this->sumWeights = array_sum($this->weights);
    }

    /**
     * @return int
     */
    public function getNumWeatherSetups()
    {
        return $this->nbWeights;
    }

    /**
     * @return int
     */
    public function getWeatherSetupsTotalWeight()
    {
        return $this->sumWeights;
    }

    /**
     * @param int $index
     *
     * @return int
     */
    public function getWeatherSetupWeight($index)
    {
        if (!isset($this->weights[$index])) {
            return false;
        }
        return $this->weights[$index];
    }

    /**
     * @param int $index
     *
     * @return array
     */
    public function getWeatherSetup($index)
    {
        if (!isset($this->setups[$index])) {
            return false;
        }
        return $this->setups[$index];
    }

}
