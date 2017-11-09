#!/usr/bin/php
<?php

require "vendor/autoload.php";

$client = new GuzzleHttp\Client();

$baseUrl  = "http://btcheat.com/";
$chestUrl = "chestcount.php";
$numbers  = "numbers.php";

$accountData = [];

if (function_exists('yaml_parse_file')) {
    $accountData = yaml_parse_file("account.yaml");
} else{
    $accountInfo = file_get_contents("account.yaml");
    $parts = explode(PHP_EOL, $accountInfo);
    foreach ($parts as $part) {
        $part = preg_replace('/[\"\,]/', '', $part);

        if (empty($part)) {continue;}
        $row = explode(":", $part);
        $key = trim(str_replace('"',"", $row[0]));
        $value = trim(str_replace(',', '', $row[1]));
        $accountData[$key] = $value;
    }
}

$headers = [
    "User-Agent"     => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36",
    "Referer"        => "http://btcheat.com/home.php",
    "X-Request-With" => "XMLHttpRequest"
];

$roll = true;
$rollsUsed = 0;
while ($roll) {
    $result = $client->get($baseUrl.$numbers, [
        "query" => $accountData,
        "headers" => $headers
    ]);

    $response = explode(":", $result->getBody()->getContents());

    if ($response[0] !== "ffa" && $response[0] !== "aaa") {
        echo "Winner! " . $response[2] . PHP_EOL;
    }

    $fh = fopen("play_log.txt","a+");
    fwrite($fh, implode(":", $response) . PHP_EOL);

    if ($response[0] == "ffa") {
        echo "Time to pick a chest!" . PHP_EOL;
        $accountData["chest"] = mt_rand(1,3);
        fwrite($fh, "choosing chest " . $accountData["chest"] . PHP_EOL);
        $chestRequest = $client->get($baseUrl.$chestUrl, [
            "query" => $accountData,
            "headers" => $headers
        ]);

        $chestResponse = explode(":", $chestRequest->getBody()->getContents());
        var_dump($chestResponse);

        fwrite($fh, implode(":", $chestResponse) . PHP_EOL);
    }

    fclose($fh);

    sleep(2);

    if ( isset($response[1]) && $response[1] == 0) {
       $roll = false;
    }

    $rollsUsed++;
}

echo sprintf("You spent %d rolls", $rollsUsed) . PHP_EOL;
