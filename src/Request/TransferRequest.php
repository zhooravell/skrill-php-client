<?php

declare(strict_types=1);

namespace Skrill\Request;

use Money\Money;
use Skrill\ValueObject\Email;
use Skrill\ValueObject\Description;
use Skrill\ValueObject\TransactionID;
use Skrill\Request\Traits\GetPayloadTrait;
use Skrill\Request\Traits\AmountFormatterTrait;

/**
 * Class TransferRequest.
 */
final class TransferRequest
{
    use GetPayloadTrait;
    use AmountFormatterTrait;

    /**
     * @param Email       $recipientEmail
     * @param Money       $amount
     * @param Description $description
     */
    public function __construct(Email $recipientEmail, Money $amount, Description $description)
    {
        $this->payload = [
            'bnf_email' => strval($recipientEmail),
            'currency' => strval($amount->getCurrency()),
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
        $this->payload['frn_trn_id'] = strval($transactionId);

        return $this;
    }
}
