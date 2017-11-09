#!/usr/bin/php
<?php

$fh = fopen("play_log.txt", "r");

$wins = 0;
$loss = 0;
$rolls = 0;
$chests = 0;

if ($fh) {
    while(($line = fgets($fh)) !== false) {
        $parts = explode(":", $line);
        switch ($parts[0]) {
            case "aaa":
                $loss++;
                break;
            case "ffa":
                $chests++;
                break;
            case "0":
                break;
            default:
                $wins++;
                break;
        }
        $rolls++;
    }
}

$totalLoggedResults = [
    'win:loss' => $wins/$loss,
    'win:rolls' => $wins/$rolls,
    'chests:rolls' => $chests/$rolls,
    'loss:rolls' => $loss/$rolls,
    'total rolls' => $rolls,
    'total wins' => $wins,
    'total loss' => $loss,
];

foreach ($totalLoggedResults as $pair=>$result) {
    echo $pair . " >>> " . $result . PHP_EOL;
}


