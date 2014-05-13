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
 * Class CNoiseValue
 */
class CNoiseValue
{
    private $abs;
    private $rand;
    private $frequency;

    /**
     * @param int $abs
     * @param int $rand
     * @param int $freq
     */
    public function __construct($abs = 0, $rand = 1, $freq = 1)
    {
        $this->abs = $abs;
        $this->rand = $rand;
        $this->frequency = $freq;
    }

    /**
     * @param CVector $pos
     *
     * @return float
     */
    public function eval_(CVector $pos)
    {
        return $this->abs + $this->rand * $this->noise($pos->mul($this->frequency));
    }

    /**
     * @param CVector $pos
     *
     * @return float
     */
    public function noise(CVector $pos)
    {
        $vd = clone $pos;
        $turb = CRandomGrid3d::getLevelSize(0) * CRandomGrid3d::evalBilinear($vd);

        $vd->mul(2);
        $turb += CRandomGrid3d::getLevelSize(1) * CRandomGrid3d::evalBilinear(
                $vd->add(CRandomGrid3d::getLevelPhase(1))
            );

        $vd->mul(2);
        $turb += CRandomGrid3d::getLevelSize(2) * CRandomGrid3d::evalBilinear(
                $vd->add(CRandomGrid3d::getLevelPhase(2))
            );

        return $turb;
    }
}
