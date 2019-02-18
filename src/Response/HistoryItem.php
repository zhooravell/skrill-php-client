<?php

declare(strict_types=1);

namespace Skrill\Response;

/**
 * Class HistoryItem.
 */
final class HistoryItem
{
    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $skrillId;

    /**
     * @var \DateTimeInterface
     */
    private $time;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $details;

    /**
     * @var float
     */
    private $lesion;

    /**
     * @var float
     */
    private $profit;

    /**
     * @var string
     */
    private $status;

    /**
     * @var float
     */
    private $balance;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $info;

    /**
     * @var string
     */
    private $paymentType;

    /**
     * @param $reference
     * @param $skrillId
     * @param \DateTimeInterface $time
     * @param $type
     * @param $details
     * @param $lesion
     * @param $profit
     * @param $status
     * @param $balance
     * @param $amount
     * @param $currency
     * @param $info
     * @param $paymentType
     */
    public function __construct(
        $reference,
        $skrillId,
        \DateTimeInterface $time,
        $type,
        $details,
        $lesion,
        $profit,
        $status,
        $balance,
        $amount,
        $currency,
        $info,
        $paymentType
    ) {
        $this->reference = $reference;
        $this->skrillId = $skrillId;
        $this->time = $time;
        $this->type = $type;
        $this->details = $details;
        $this->lesion = $lesion;
        $this->profit = $profit;
        $this->status = $status;
        $this->balance = $balance;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->info = $info;
        $this->paymentType = $paymentType;
    }

    /**
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return float
     */
    public function getProfit()
    {
        return $this->profit;
    }

    /**
     * @return float
     */
    public function getLesion()
    {
        return $this->lesion;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getSkrillId()
    {
        return $this->skrillId;
    }
}
