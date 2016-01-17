<?php

require __DIR__ . '/vendor/autoload.php';

use Ryzom\RyzomWeather;

// get current server tick
//$xml = ryzom_time_api();
//$tick = (int)$xml->server_tick;
$tick = 1165305274;

// setup weather prediction
$ryzomWeather = new RyzomWeather();
$ryzomWeather->setServerTick($tick);

// display weather on continents
$continents = array(
    'tryker',
    'matis',
    'fyros',
    'zorai',
    'newbieland',
    'nexus',
    'sources',
    'bagne',
    'terre',
    'route_gouffre',
    'kitiniere',
    'matis_island',
);

printf("Game tick: %d\n", $tick);
printf("Weather cycle: %d\n", $ryzomWeather->getWeatherCycle());
foreach ($continents as $continent) {
    $weather = $ryzomWeather->getWeather($continent);
    printf("%.3f (%s): %s\n", $weather->getWeather(), $weather->getWeatherDepositCondition(), $continent);
}


