<?php

namespace Achse\ShapeShiftIo\Tests;

require_once __DIR__ . '/bootstrap.php';

use Achse\ShapeShiftIo\Client;
use Achse\ShapeShiftIo\Coins;
use Achse\ShapeShiftIo\Test\MyAssert;
use Nette\SmartObject;
use Tester\Assert;
use Tester\TestCase;

class ClientTest extends TestCase
{

    use SmartObject;

    const DUMMY_ADDRESS = '0x1234'; // Todo: create some dummy transaction

    public function testRate()
    {
        $rate = (new Client())->getRate(Coins::BITCOIN, Coins::LITECOIN);
        MyAssert::positiveFloat($rate);
    }

    /**
     * @throws \Achse\ShapeShiftIo\UnknownPairException
     */
    public function testUnknownCoinPairException()
    {
        (new Client())->getRate('foo', 'bar');
    }

    public function testLimit()
    {
        $limit = (new Client())->getLimit(Coins::BITCOIN, Coins::LITECOIN);
        MyAssert::positiveFloat($limit);
    }

    public function testMarketAll()
    {
        $marketInfo = (new Client())->getMarketInfo();
        Assert::true(count($marketInfo) > 0, 'There should be some data');

        $pair = sprintf('%s_%s', Coins::BITCOIN, Coins::ETHEREUM);
        Assert::equal($pair, $marketInfo[$pair]->pair);
        MyAssert::positiveFloat($marketInfo[$pair]->rate);
        MyAssert::positiveFloat($marketInfo[$pair]->limit);
        MyAssert::positiveFloat($marketInfo[$pair]->min);
        MyAssert::positiveFloat($marketInfo[$pair]->minerFee);
    }

    public function testTimeRemaining()
    {
        $timeRemaining = (new Client())->getTimeRemaining(self::DUMMY_ADDRESS);
        Assert::equal(0, $timeRemaining);
        
        // Todo: create transaction in test and try that TimeRemaining > 0  
    }

}

(new ClientTest())->run();
