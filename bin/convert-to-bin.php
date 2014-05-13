<?php

$input = 'raw-weather.txt';
$output = 'raw-weather.bin';
if (!file_exists($input)) {
    die("- file ($input) does not exist\n");
}

$lines = file($input);
$values = '';
$first = true;
foreach ($lines as $line) {
    if ($line[0] == ';') {
        continue;
    }
    $pairs = explode(',', $line);
    if ($first) {
        // 32bit, little-endian
        $values .= pack('V', $pairs[0]);
        $first = false;
    }
    // float, machine dependant - expected to be 32bit, little-endian
    $values .= pack('f', $pairs[1]);
}

file_put_contents($output, $values);

