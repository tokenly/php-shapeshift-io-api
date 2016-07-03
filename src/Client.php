<?php

namespace Achse\ShapeShiftIo;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use LogicException;
use Nette\NotImplementedException;
use Nette\SmartObject;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Psr\Http\Message\ResponseInterface;
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
     * @return stdClass[]
     */
    public function getMarketInfo(string $coin1 = null, string $coin2 = null) : array
    {
        return $this->get(sprintf('%s/%s', Resources::MARKET_INFO, $this->getPair($coin1, $coin2)));
    }

    /**
     * @see https://info.shapeshift.io/api#api-4
     *
     * @param int $max
     * @return stdClass[]
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
     * @return stdClass
     * @throws RequestFailedException
     * @throws ApiErrorException
     */
    public function getStatusOfDepositToAddress(string $address) : stdClass
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
     * @return stdClass
     */
    public function getSupportedCoins() : stdClass
    {
        return $this->get(Resources::LIST_OF_SUPPORTED_COINS);
    }

    /**
     * @see https://info.shapeshift.io/api#api-105
     *
     * @return stdClass[]
     */
    public function getListAOfTransactionsByApiKey(string $apiKey) : array
    {
        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-106
     *
     * @param string $address
     * @return stdClass[]
     */
    public function getTransactionsByOutputAddress(string $address) : array
    {
        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-7
     *
     * @param string $withdrawalAddress
     * @param string $coin1
     * @param string $coin2
     * @param string|null $returnAddress
     * @param string|null $destinationTag
     * @param string|null $rsAddress
     * @param string|null $apiKey
     */
    public function createTransaction(
        string $withdrawalAddress,
        string $coin1,
        string $coin2,
        string $returnAddress = null,
        string $rsAddress = null,
        string $destinationTag = null,
        string $apiKey = null
    ) {
        $input = $this->buildSubmitTransactionBodyObject(
            $withdrawalAddress,
            $coin1,
            $coin2,
            $returnAddress,
            $rsAddress,
            $destinationTag,
            $apiKey
        );
        Json::encode($input);

        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-8
     *
     * @param string $email
     * @param string $transactionId
     */
    public function requestEmailReceipt(string $email, string $transactionId) : void
    {
        $input = new stdClass();
        $input->email = $email;
        $input->txid = $transactionId;
        $body = Json::encode($input);

        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-9
     *
     * @param float $amount
     * @param string $withdrawalAddress
     * @param string $coin1
     * @param string $coin2
     * @param string|null $returnAddress
     * @param string|null $rsAddress
     * @param string|null $destinationTag
     * @param string|null $apiKey
     */
    public function createFixedAmountTransaction(
        float $amount,
        string $withdrawalAddress,
        string $coin1,
        string $coin2,
        string $returnAddress = null,
        string $rsAddress = null,
        string $destinationTag = null,
        string $apiKey = null
    ) {
        $input = $this->buildSubmitTransactionBodyObject(
            $withdrawalAddress,
            $coin1,
            $coin2,
            $returnAddress,
            $rsAddress,
            $destinationTag,
            $apiKey
        );
        $input->amount = $amount;
        Json::encode($input);

        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-9
     *
     * @param float $amount
     * @param string $coin1
     * @param string $coin2
     */
    public function getAmountForTransaction(float $amount, string $coin1, string $coin2)
    {
        $input = $this->buildSubmitTransactionBodyObject(null, $coin1, $coin2);
        $input->amount = $amount;
        Json::encode($input);

        throw new NotImplementedException();
    }

    /**
     * @see ěhttps://info.shapeshift.io/#api-108
     *
     * @param string $address
     */
    public function cancelTransaction(string $address) : void
    {
        throw new NotImplementedException();
    }

    /**
     * @see https://info.shapeshift.io/#api-107
     *
     * @param string $address
     * @param string $coin
     * @return stdClass
     */
    public function validateAddress(string $address, string $coin) : stdClass
    {
        $result = $this->get(sprintf('%s/%s/%s', Resources::VALIDATE_ADDRESS, $address, $coin));

        if (!isset($result->isValid) && isset($result->isvalid)) {
            $result->isValid = $result->isvalid;
            unset ($result->isvalid);
        }

        return $result;
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
            $this->handleGuzzleRequestException($exception);
        }

        return $this->processResult($url, $response);
    }

    /**
     * @param string $url
     * @param array $options
     * @return array|stdClass
     * @throws ApiErrorException
     * @throws RequestFailedException
     */
    private function post(string $url, array $options = [])
    {
        try {
            $response = $this->guzzleClient->post($url, $options);
        } catch (RequestException $exception) {
            $this->handleGuzzleRequestException($exception);
        }

        return $this->processResult($url, $response);
    }

    /**
     * @param array|stdClass $result
     * @param string $url
     * @throws ApiErrorException
     */
    private function checkErrors($result, string $url)
    {
        $error = $this->findErrorInResult($result);

        if ($error !== null && !$this->isEndpointOkWithError($url)) {

            if ($error === 'Unknown pair') {
                throw new UnknownPairException('Coin identifiers pair unknown.');
            }

            throw new ApiErrorException($error);
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

    /**
     * ShapeShift API does NOT provide 400 status code on error and for some endpoints
     * can be $result->error success response.
     *
     * @param string $url
     * @return bool
     */
    private function isEndpointOkWithError(string $url) : bool
    {
        return Strings::startsWith($url, Resources::VALIDATE_ADDRESS);
    }

    /**
     * @param stdClass|array $result
     * @return string|stdClass|null
     */
    private function findErrorInResult($result)
    {
        return $result instanceof stdClass && isset($result->error) ? $result->error : null;
    }

    /**
     * @param RequestException $exception
     * @throws RequestFailedException
     */
    private function handleGuzzleRequestException(RequestException $exception)
    {
        $message = sprintf('Request failed due: "%s".', $exception->getMessage());
        throw new RequestFailedException($message, $exception->getCode(), $exception);
    }

    /**
     * @param string $url
     * @param ResponseInterface $response
     * @return array|stdClass
     * @throws ApiErrorException
     */
    private function processResult(string $url, ResponseInterface $response)
    {
        $result = Json::decode($response->getBody()->getContents());
        $this->checkErrors($result, $url);

        return $result;
    }

    /**
     * @param string|null $withdrawalAddress
     * @param string $coin1
     * @param string $coin2
     * @param string|null $returnAddress
     * @param string|null $rsAddress
     * @param string|null $destinationTag
     * @param string|null $apiKey
     * @return stdClass
     */
    private function buildSubmitTransactionBodyObject(
        string $withdrawalAddress = null,
        string $coin1,
        string $coin2,
        string $returnAddress = null,
        string $rsAddress = null,
        string $destinationTag = null,
        string $apiKey = null
    ) : stdClass
    {
        $input = new stdClass();
        if ($withdrawalAddress !== null) {
            $input->withdrawal = $withdrawalAddress;
        }
        $input->pair = sprintf('%s_%s', $coin1, $coin2);
        if ($returnAddress !== null) {
            $input->returnAddress = $returnAddress;
        }
        if ($withdrawalAddress !== null) {
            $input->destTag = $destinationTag;
        }
        if ($rsAddress !== null) {
            $input->rsAddress = $rsAddress;
        }
        if ($apiKey !== null) {
            $input->apiKey = $apiKey;
        }

        return $input;
    }

}
