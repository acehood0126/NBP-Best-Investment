<?php

require __DIR__ . '/vendor/autoload.php';

if (count($argv) < 4) {
    die('Error: Too few arguments for script!');
}

$investor = new \App\Investor('http://api.nbp.pl/api/cenyzlota/');

$money = $argv[1];
$startDate = $argv[2];
$endDate = $argv[3];

$goldInvest = $investor->bestGoldProfit($money, $startDate, $endDate);

if ($goldInvest['profit'] > 0) {
    echo sprintf("Best buy price on:    %s (price %.2f zl) \n", $goldInvest['buyDate'], $goldInvest['buyPrice']);
    echo sprintf("Best sell price on:   %s (price %.2f zl) \n", $goldInvest['saleDate'], $goldInvest['salePrice']);
    echo sprintf("Best profit:          %.2f zl (percentage profit %.2f %%) \n", $goldInvest['profit'], $goldInvest['profitPercent']);
} else {
    echo 'In given range of time there was not possible to get any profit from gold buy/sell process.';
}
