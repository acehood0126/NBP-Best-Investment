<?php

use App\Investor;
use PHPUnit\Framework\TestCase;

class InvestorTest extends TestCase
{
    private $investor;

    public function setUp() {
        $this->investor = new Investor('http://api.nbp.pl/api/cenyzlota/');
    }

    public function testBasicGettingTheBestProfit()
    {
        $mockData = json_decode(file_get_contents(__DIR__ . '/data/data1.json'));

        $goldInvest = $this->investor->getBestProfit($mockData);

        $this->assertEquals("2018-02-01", $goldInvest['buyDate']);
        $this->assertEquals("2018-02-03", $goldInvest['saleDate']);
        $this->assertEquals(160.23, $goldInvest['buyPrice']);
        $this->assertEquals(176.76, $goldInvest['salePrice']);
    }

    public function testOtherGettingTheBestProfit()
    {
        $mockData = json_decode(file_get_contents(__DIR__ . '/data/data2.json'));

        $goldInvest = $this->investor->getBestProfit($mockData);

        $this->assertEquals("2018-02-07", $goldInvest['buyDate']);
        $this->assertEquals("2018-02-09", $goldInvest['saleDate']);
        $this->assertEquals(159.23, $goldInvest['buyPrice']);
        $this->assertEquals(173.33, $goldInvest['salePrice']);
    }

    public function testNoProfit()
    {
        $mockData = json_decode(file_get_contents(__DIR__ . '/data/data3.json'));

        $goldInvest = $this->investor->getBestProfit($mockData);

        $this->assertEquals(null, $goldInvest['buyDate']);
        $this->assertEquals(null, $goldInvest['saleDate']);
        $this->assertEquals(null, $goldInvest['buyPrice']);
        $this->assertEquals(null, $goldInvest['salePrice']);
        $this->assertEquals(0, $goldInvest['finalProfit']);
    }
}
