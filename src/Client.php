<?php

namespace Achse\ShapeShiftIo;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use LogicException;
use Nette\NotImplementedException;
use Nette\SmartObject;
use Nette\Utils\Json;
use stdClass;

class Client
{

    use SmartObject;

    const DEFAULT_BASE_URL = 'https://shapeshift.io';

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var GuzzleClient
     */
    private $guzzleClient;

    /**
     * @param string $baseUrl
     */
    public function __construct(string $baseUrl = self::DEFAULT_BASE_URL)
    {
        $this->baseUrl = $baseUrl;
        $this->guzzleClient = new GuzzleClient(['base_uri' => $baseUrl]);
    }

    /**
     * @see https://info.shapeshift.io/api#api-2
     *
     * @param string $coin1
     * @param string $coin2
     * @return float
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getRate(string $coin1, string $coin2) : float
    {
        return (float)$this->get(sprintf('%s/%s', Resources::RATE, $this->getPair($coin1, $coin2)))->rate;
    }

    /**
     * @see https://info.shapeshift.io/api#api-3
     *
     * @param string $coin1
     * @param string $coin2
     * @return float
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getLimit(string $coin1, string $coin2) : float
    {
        return (float)$this->get(sprintf('%s/%s', Resources::LIMIT, $this->getPair($coin1, $coin2)))->limit;
    }

    /**
     * @see https://info.shapeshift.io/api#api-103
     *
     * @param string|null $coin1
     * @param string|null $coin2
     * @return MarketInfo
     */
    public function getMarketInfo(string $coin1 = null, string $coin2 = null) : MarketInfo
    {
        $result = $this->get(sprintf('%s/%s', Resources::MARKET_INFO, $this->getPair($coin1, $coin2)));

        return new MarketInfo($result);
    }

    /**
     * @see https://info.shapeshift.io/api#api-4
     *
     * @param int $max
     * @return SomeSmallerWtfTransaction[] // Todo: solve this no ide what this enpoint means
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getRecentTransactionList(int $max) : array
    {
        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/api#api-5
     *
     * @param string $address
     * @return TransactionStatus
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getStatusOfDepositToAddress(string $address) : TransactionStatus
    {
        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/api#api-6
     *
     * @param string $address
     * @return int
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getTimeRemaining(string $address) : int
    {
        return (int)$this->get(sprintf('%s/%s', Resources::TIME_REMAINING, $address))->seconds_remaining;
    }

    /**
     * @see https://info.shapeshift.io/api#api-104
     *
     * @return stdClass[]
     */
    public function getListOfSupportedCoins() : array
    {
        return $this->get(Resources::LIST_OF_SUPPORTED_COINS);
    }

    /**
     * @see https://info.shapeshift.io/api#api-105
     * 
     * @return Transaction[]
     */
    public function getListAOfTransactionsByApiKey(string $apiKey) : array
    {
        throw new NotImplementedException();
    }
    
    /**
     * @param string $url
     * @param array $options
     * @return stdClass|array
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    private function get(string $url, array $options = [])
    {
        try {
            $response = $this->guzzleClient->get($url, $options);
        } catch (RequestException $exception) {
            throw new RequestFailedException(
                sprintf('Request failed due: "%s".', $exception->getMessage()),
                $exception->getCode(),
                $exception
            );
        }

        $result = Json::decode($response->getBody()->getContents());
        $this->checkErrors($result);

        return $result;
    }

    /**
     * @param stdClass|array $result
     * @throws ApiErrorException
     */
    private function checkErrors($result)
    {
        if ($result instanceof stdClass && isset($result->error)) {
            if ($result->error === 'Unknown pair') {
                throw new UnknownPairException('Coin identifiers pair unknown.');
            } else {
                throw new ApiErrorException($result->error);
            }
        }
    }

    /**
     * @param string|null $coin1
     * @param string|null $coin2
     * @return string
     */
    private function getPair(string $coin1 = null, string $coin2 = null) : string
    {
        if (($coin1 === null || $coin2 === null) && $coin1 !== $coin2) {
            throw new LogicException('You must provide both or none of the coins.');
        }

        return $coin1 !== null ? sprintf('%s_%s', $coin1, $coin2) : '';
    }

}
