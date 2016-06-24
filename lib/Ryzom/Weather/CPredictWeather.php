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

use Nel\Misc\CNoiseValue;
use Nel\Misc\CVector;

/**
 * Class CPredictWeather
 */
class CPredictWeather
{
    /** @var CNoiseValue */
    private $nv;

    /** @var resource */
    private $fp;

    /* @var int */
    private $fileSize;

    /** @var int */
    private $startCycle;

    /** @var string */
    private $rawWeatherFile;

    /**
     * Init
     */
    public function __construct()
    {
        $this->nv = new CNoiseValue();

        $this->rawWeatherFile = __DIR__.'/../../../resources/raw-weather.bin';
    }

    /**
     * Closes raw-weather.bin file if it was opened previously
     */
    public function __destruct()
    {
        if ($this->fp !== null) {
            fclose($this->fp);
        }
    }

    /**
     * @param int $cycle weather cycle we in (hours/3)
     * @param CWeatherFunction $wf cycle season weather function (weights)
     *
     * @return WeatherValue
     */
    public function getCycleWeatherValue($cycle, CWeatherFunction $wf)
    {
        $numWS = $wf->getNumWeatherSetups();
        if (!$numWS) {
            return 0;
        }

        /* old weather
        $noiseValue = $this->rawWeatherProvider($cycle);

        // sum all weights, usually adds up to 100
        $value = (int)($noiseValue * $wf->getWeatherSetupsTotalWeight());
         */

        $noiseValue = \Nel\Misc\wang_hash64($cycle);
        // noise is 64bit unsigned, so use GMP library
        // value = wangHash64(cycle) % wf.getWeatherSetupsTotalWeight();
        $value = gmp_strval(gmp_mod($noiseValue, $wf->getWeatherSetupsTotalWeight()));

        $currWeight = 0;
        for ($k = 0; $k < $numWS; $k++) {
            $weight = $wf->getWeatherSetupWeight($k);
            if ($value >= $currWeight && $value < $currWeight + $weight) {
                $scaledWeather = (($value - $currWeight) / $weight) + $k;
                $weather = $scaledWeather / $numWS;

                return new WeatherValue($k, $weather, $wf);
            }
            $currWeight += $weight;
        }

        return new WeatherValue($numWS, 1, $wf);
    }

    /**
     * Return either pre-calculated value that should be more accurate
     * or calculate raw weather value on the fly when pre-calculated
     * has runned out of values
     *
     * @param int $cycle
     *
     * @return float
     */
    private function rawWeatherProvider($cycle)
    {
        $result = $this->getCachedRawWeather($cycle);
        if ($result !== false) {
            return $result;
        }

        return $this->getCalculatedRawWeather($cycle);
    }

    /**
     * Read single value from raw-weather.bin file
     *
     * File pointer is cached in class variable, as is file size and start cycle
     * File is closed on __destruct() call
     *
     * @param int $cycle
     *
     * @return float
     */
    private function getCachedRawWeather($cycle)
    {
        if ($this->fp === null) {
            $this->fileSize = filesize($this->rawWeatherFile);
            $this->fp = fopen($this->rawWeatherFile, 'r');

            // 32bit int, little-endian
            $b = fread($this->fp, 4);
            $tmp = unpack('V', $b);
            $this->startCycle = $tmp[1];
        }

        $seek = 4 + ($cycle - $this->startCycle) * 4;
        if ($seek < 0 || $seek >= $this->fileSize) {
            return false;
        }

        fseek($this->fp, $seek);
        $b = fread($this->fp, 4);
        $tmp = unpack('f', $b);
        return $tmp[1];
    }

    /**
     * Calculate raw weather in php.
     *
     * Gives different result because of floating point rounding
     *
     * @param int $cycle
     *
     * @return float
     */
    private function getCalculatedRawWeather($cycle)
    {
        // OptFastFloorBegin()
        // _RC_DOWN - 1.2 => 1, 1.6 => 1, -1.2 => -2, -1.6 => -2 ==== php floor()
        // _PC_53   - 53bit precision

        $pos = new CVector($cycle * 0.99524, $cycle * 0.85422, $cycle * -0.45722);
        $noiseValue = $this->nv->eval_($pos);
        // OptFastFloorEnd();

        return fmod($noiseValue * 10, 1);
    }
}
