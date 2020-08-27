<?php

declare(strict_types=1);

namespace Skrill\Request;

use Money\Money;
use Skrill\ValueObject\Description;
use Skrill\ValueObject\TransactionID;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\Request\Traits\AmountFormatterTrait;

/**
 * Class PayoutRequest.
 */
final class PayoutRequest
{
    use GetPayloadTrait;
    use AmountFormatterTrait;

    /**
     * @param Money       $amount
     * @param Description $description
     */
    public function __construct(Money $amount, Description $description)
    {
        $this->payload = [
            'currency' => (string)$amount->getCurrency(),
            'amount' => $this->formatToFloat($amount),
            'subject' => $description->getSubject(),
            'note' => $description->getText(),
        ];
    }

    /**
     * Your reference ID (must be unique if submitted).
     *
     * @param TransactionID $transactionId
     *
     * @return $this
     */
    public function setReferenceTransaction(TransactionID $transactionId): self
    {
        $this->payload['frn_trn_id'] = (string)$transactionId;

        return $this;
    }

    /**
     * Set transaction_id of the original payment for quick checkout payouts
     *
     * @param TransactionID $transactionId instance
     * @return $this
     */
    public function setOriginalTransactionId(TransactionID $transactionId): self
    {
        $this->payload['transaction_id'] = (string)$transactionId;

        return $this;
    }

    /**
     * Setting The Skrill transaction ID of the original payment.
     *
     * Used for preparing Neteller payouts with initial neteller's successful deposit
     * transaction ID.
     *
     * @param TransactionID $transactionId instance
     * @return $this
     */
    public function setSkrillOriginalTransactionId(TransactionID $transactionId): self
    {
        $this->payload['mb_transaction_id'] = (string)$transactionId;

        return $this;
    }
}
