<?php

namespace App;


use DateTime;
use Exception;

/**
 * Class for calculating gains from investments
 *
 * Class Investor
 * @package App
 */
class Investor
{
    /**
     * Limit of days that we can get data for at once
     */
    protected const DAYS_LIMIT = 367;

    /**
     * API url
     *
     * @var string
     */
    protected $url;

    /**
     * Investor constructor
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Calculate optimal buy and sell prices of gold in given time period
     * for get the best profit
     *
     * @param float $money      Invested amount of money
     * @param string $startDate Period start date
     * @param string $endDate   Period end date
     * @return array|null
     */
    public function bestGoldProfit(float $money, string $startDate, string $endDate)
    {
        $goldCosts = $this->getGoldCostData($startDate, $endDate);

        $bestProfit = $this->getBestProfit($goldCosts);

        return [
            'buyDate' => $bestProfit['buyDate'],
            'buyPrice' => $bestProfit['buyPrice'],
            'saleDate' => $bestProfit['saleDate'],
            'salePrice' => $bestProfit['salePrice'],
            'profitPercent' => $bestProfit['finalProfit'] * 100,
            'profit' => ($money * $bestProfit['finalProfit']) - $money
        ];
    }

    /**
     * Helper method to get gold cost data from given time range
     *
     * NBP gold cost API is limited to certain number of days per one request
     * so we need multimple requests to get data for period longer than that
     * limit.
     *
     * @param string $startDate Start date
     * @param string $endDate   End date
     * @return array
     */
    public function getGoldCostData(string $startDate, string $endDate): array
    {
        $dStart = new DateTime($startDate);
        $dEnd  = new DateTime($endDate);

        $dDiff = $dStart->diff($dEnd);
        $data = [];

        try {
            if ($dDiff->days < self::DAYS_LIMIT) {
                // If requested time range is lower that the API limit
                $data = CurlHelper::get($this->url . $startDate . '/' . $endDate);
            } else {
                // If requested time period is larger than the API limit we must divide the given
                // range to smaller parts, get data for them separately and merge them in one data set
                $start = $end = new DateTime($startDate);

                while ($start->diff($dEnd)->days > 0) {
                    $partial = CurlHelper::get($this->url . $start->format('Y-m-d') . '/' . $end->format('Y-m-d'));
                    $data = array_merge($data, $partial);

                    $start = clone $end;

                    if (($start->diff($dEnd))->days > self::DAYS_LIMIT) {
                        $end->modify('+' . self::DAYS_LIMIT . ' day');
                    } else {
                        $end = new DateTime($endDate);
                    }
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return $data;
    }

    /**
     * Recursive method implementing divide and conquer strategy to find the best
     * buy and sell points in prices dataset
     *
     * @param array $prices Dataset/array containing gold prices
     * @return array|mixed
     */
    public function getBestProfit(array $prices)
    {
        $length = count($prices);

        // If dataset contains one or zero elements then the profit is zero
        if ($length <= 1) {
            return [
                'buyDate' => null,
                'buyPrice' => null,
                'saleDate' => null,
                'salePrice' => null,
                'finalProfit' => 0
            ];
        }

        // Divide dataset to two parts
        $leftHalf = array_slice($prices, 0, $length / 2);
        $rightHalf = array_slice($prices, $length / 2);

        // Calculate the best profit separately for "left" and "right" part
        $leftBest = $this->getBestProfit($leftHalf);
        $rightBest = $this->getBestProfit($rightHalf);

        // Get the lowest price from left part and highest from right part and calculate best profit for them
        $leftMin = array_reduce($leftHalf, function ($prev, $next) {
            return ($prev === null || $next->cena < $prev->cena) ? $next : $prev;
        });

        $rightMax = array_reduce($rightHalf, function ($prev, $next) {
            return ($prev === null || $next->cena > $prev->cena) ? $next : $prev;
        });

        if ($rightMax->cena > $leftMin->cena) {
            $bothBest = [
                'buyDate' => $leftMin->data,
                'buyPrice' => $leftMin->cena,
                'saleDate' => $rightMax->data,
                'salePrice' => $rightMax->cena,
                'finalProfit' => $rightMax->cena / $leftMin->cena
            ];
        } else {
            $bothBest = [
                'buyDate' => null,
                'buyPrice' => null,
                'saleDate' => null,
                'salePrice' => null,
                'finalProfit' => 0
            ];
        }

        // Choose the best profit from three results and return it
        $bestProfit = array_reduce([$leftBest, $rightBest, $bothBest], function ($first, $second) {
            return ($first === null || $second['finalProfit'] > $first['finalProfit']) ? $second : $first;
        });

        return $bestProfit;
    }
}
