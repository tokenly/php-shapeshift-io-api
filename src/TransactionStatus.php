<?php

namespace Achse\ShapeShiftIo;

use Nette\SmartObject;
use stdClass;

class TransactionStatus
{

    use SmartObject;

    const RECEIVED = 'received';
    const COMPLETE = 'complete';
    const FAILED = 'failed';

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $errorText = '';

    /**
     * @var string
     */
    private $withdrawalAddress = '';

    /**
     * @var float
     */
    private $incomingCoinAmount = 0.0;

    /**
     * @var string
     */
    private $incomingCoinName = '';

    /**
     * @var float
     */
    private $outgoingCoinAmount = 0.0;

    /**
     * @var string
     */
    private $outgoingCoinName = '';

    /**
     * @var string
     */
    private $transactionId = '';

    /**
     * @param stdClass $apiStatus
     */
    public function __construct(stdClass $apiStatus)
    {
        $this->status = $apiStatus->status;
        $this->address = $apiStatus->address;

        $this->errorText = $this->isFailed() ? $apiStatus->error : '';
        if ($this->isComplete()) {
            $this->withdrawalAddress = $apiStatus->withdraw;
            $this->incomingCoinAmount = (float)$apiStatus->incomingCoin;
            $this->incomingCoinName = $apiStatus->incomingType;
            $this->outgoingCoinAmount = $apiStatus->outgoingCoin;
            $this->outgoingCoinName = $apiStatus->outgoingType;
            $this->transactionId = $apiStatus->transaction;
        }
    }

    /**
     * @return bool
     */
    public function isReceived() : bool
    {
        return $this->status === self::RECEIVED;
    }

    /**
     * @return bool
     */
    public function isComplete() : bool
    {
        return $this->status === self::COMPLETE;
    }

    /**
     * @return bool
     */
    public function isFailed() : bool
    {
        return $this->status === self::FAILED;
    }

    /**
     * @return string
     */
    public function getAddress() : string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getErrorText() : string
    {
        return $this->errorText;
    }

    /**
     * @return string
     */
    public function getWithdrawalAddress() : string
    {
        return $this->withdrawalAddress;
    }

    /**
     * @return float
     */
    public function getIncomingCoinAmount() : float
    {
        return $this->incomingCoinAmount;
    }

    /**
     * @return string
     */
    public function getIncomingCoinName() : string
    {
        return $this->incomingCoinName;
    }

    /**
     * @return float
     */
    public function getOutgoingCoinAmount() : float
    {
        return $this->outgoingCoinAmount;
    }

    /**
     * @return string
     */
    public function getOutgoingCoinName() : string
    {
        return $this->outgoingCoinName;
    }

    /**
     * @return string
     */
    public function getTransactionId() : string
    {
        return $this->transactionId;
    }

}
