<?php

/**
 * @testCase
 */

namespace Achse\ShapeShiftIo\Tests;

require_once __DIR__ . '/bootstrap.php';

use Achse\ShapeShiftIo\Client;
use Achse\ShapeShiftIo\Coins;
use Achse\ShapeShiftIo\Test\MyAssert;
use Nette\SmartObject;
use stdClass;
use Tester\Assert;
use Tester\TestCase;

class ClientTest extends TestCase
{

    use SmartObject;

    const DUMMY_ADDRESS = '0x1234';

    public function testRate()
    {
        $rate = (new Client())->getRate(Coins::BITCOIN, Coins::LITECOIN);
        MyAssert::positiveFloat($rate);
    }

    /**
     * @throws \Achse\ShapeShiftIo\ApiError\UnknownPairException
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
        $coinItems = array_filter(
            $marketInfo,
            function (stdClass $coinItem) use ($pair) : bool {
                return $coinItem->pair === $pair;
            }
        );
        $coinItem = reset($coinItems);
        Assert::equal($pair, $coinItem->pair);
        MyAssert::positiveFloat($coinItem->rate);
        MyAssert::positiveFloat($coinItem->limit);
        MyAssert::positiveFloat($coinItem->min);
        MyAssert::positiveFloat($coinItem->minerFee);
    }

    public function testRecentTransactionList()
    {
        $transactions = (new Client())->getRecentTransactionList(100);
        Assert::true(count($transactions) > 0, 'There should be some transactions.');
        /** @var stdClass $firstTransaction */
        $firstTransaction = reset($transactions);
        Assert::true(isset($firstTransaction->curIn));
        Assert::true(isset($firstTransaction->curOut));
        Assert::true(isset($firstTransaction->timestamp));
        Assert::true(isset($firstTransaction->amount));
    }

    public function testStatusOfDepositToAddress()
    {
        $transactions = (new Client())->getStatusOfDepositToAddress('1H7HdTSLsHj31Pnixizhmcck49VJRNg5Pn');
        Assert::equal('no_deposits', $transactions->status);
        Assert::equal('1H7HdTSLsHj31Pnixizhmcck49VJRNg5Pn', $transactions->address);
    }

    /**
     * @throws \Achse\ShapeShiftIo\ApiError\NotDepositAddressException
     */
    public function testStatusOfDepositToAddressFailed()
    {
        (new Client())->getStatusOfDepositToAddress('tralala');
    }

    /**
     * @throws \Achse\ShapeShiftIo\ApiError\NoPendingTransactionException
     */
    public function testTimeRemainingNoPendingTransaction()
    {
        (new Client())->getTimeRemaining('tralala');
    }

    public function testGetSupportedCoins()
    {
        $supportedCoins = (new Client())->getSupportedCoins();

        Assert::equal('Bitcoin', $supportedCoins->{Coins::BITCOIN}->name);
        Assert::equal(Coins::ETHEREUM, $supportedCoins->{Coins::ETHEREUM}->symbol);
    }

    public function testGetListAOfTransactionsByApiKey()
    {
        $result = (new Client())->getListAOfTransactionsByApiKey('YOLOOOOO');
        Assert::equal([], $result);
    }

    public function testGetTransactionsByOutputAddress()
    {
        $result = (new Client())->getTransactionsByOutputAddress('tralala', 'YOLOOOOO');
        Assert::equal([], $result);
    }

    /**
     * @dataProvider getDataForValidateAddress
     *
     * @param bool $expectedValid
     * @param string $expectedErrorMessage
     * @param string $address
     * @param string $coin
     */
    public function testValidateAddress(
        bool $expectedValid,
        string $expectedErrorMessage,
        string $address,
        string $coin
    ) {
        $result = (new Client())->validateAddress($address, $coin);
        Assert::equal($expectedValid, $result->isValid);
        if (!$expectedValid) {
            Assert::equal($expectedErrorMessage, $result->error);
        }
    }

    public function testCreateTransaction()
    {
        $result = (new Client())->createTransaction(
            '0x123f681646d4a755815f9cb19e1acc8565a0c2ac',
            Coins::BITCOIN,
            Coins::ETHEREUM,
            '1HLjjjSPzHLNn5GTvDNSGnhBqHEF7nZxNZ'
        );

        Assert::true(is_string($result->orderId));
        Assert::true(is_string($result->deposit));
        Assert::equal(Coins::BITCOIN, $result->depositType);
        Assert::equal('0x123f681646d4a755815f9cb19e1acc8565a0c2ac', $result->withdrawal);
        Assert::equal(Coins::ETHEREUM, $result->withdrawalType);
        Assert::equal(null, $result->public);
        Assert::equal('1HLjjjSPzHLNn5GTvDNSGnhBqHEF7nZxNZ', $result->returnAddress);
        Assert::equal(Coins::BITCOIN, $result->returnAddressType);
    }

    /**
     * @throws \Achse\ShapeShiftIo\ApiError\NoTransactionFoundException
     */
    public function testRequestEmailReceipt()
    {
        (new Client())->requestEmailReceipt('rainhard@tester.com', '123BC');
    }

    public function testCreateFixedAmountTransaction()
    {
        $result = (new Client())->createFixedAmountTransaction(
            0.5,
            '0x123f681646d4a755815f9cb19e1acc8565a0c2ac',
            Coins::BITCOIN,
            Coins::ETHEREUM,
            '1HLjjjSPzHLNn5GTvDNSGnhBqHEF7nZxNZ'
        );
        
        Assert::true(is_string($result->orderId));
        Assert::equal('btc_eth', $result->pair);
        Assert::equal('0x123f681646d4a755815f9cb19e1acc8565a0c2ac', $result->withdrawal);
        Assert::equal('0.5', $result->withdrawalAmount);
        Assert::true(is_string($result->deposit));
        Assert::true(is_numeric($result->depositAmount));
        Assert::true(is_numeric($result->expiration));
        Assert::true(is_numeric($result->quotedRate));
        Assert::true(is_numeric($result->maxLimit));
        Assert::equal('1HLjjjSPzHLNn5GTvDNSGnhBqHEF7nZxNZ', $result->returnAddress);
        Assert::equal('shapeshift', $result->apiPubKey);
        Assert::true(is_numeric($result->minerFee));
    }
    
    /**
     * @throws \Achse\ShapeShiftIo\ApiError\TransactionNotCancelledException
     */
    public function testCancelTransaction()
    {
        (new Client())->cancelTransaction('1HB5XMLmzFVj8ALj6mfBsbifRoD4miY36v');
    }

    /**
     * @return array
     */
    public function getDataForValidateAddress()
    {
        return [
            [false, 'Invalid address.', self::DUMMY_ADDRESS, Coins::LITECOIN],
            [true, '', '0x123f681646d4a755815f9cb19e1acc8565a0c2ac', Coins::ETHEREUM],
            [true, '', '1HLjjjSPzHLNn5GTvDNSGnhBqHEF7nZxNZ', Coins::BITCOIN],
        ];
    }

}

(new ClientTest())->run();
