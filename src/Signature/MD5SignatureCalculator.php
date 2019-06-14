<?php

declare(strict_types=1);

namespace Skrill\Signature;

use Money\Money;
use Money\MoneyFormatter;
use Skrill\ValueObject\Signature;
use Skrill\ValueObject\SecretWord;
use Skrill\ValueObject\MerchantID;
use Skrill\ValueObject\TransactionID;

/**
 * Class MD5SignatureCalculator.
 */
final class MD5SignatureCalculator implements SignatureCalculator
{
    /**
     * @var SecretWord
     */
    private $secretWord;

    /**
     * @var
     */
    private $merchantId;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @param SecretWord     $secretWord
     * @param MerchantID     $merchantId
     * @param MoneyFormatter $moneyFormatter
     */
    public function __construct(SecretWord $secretWord, MerchantID $merchantId, MoneyFormatter $moneyFormatter)
    {
        $this->secretWord = $secretWord;
        $this->merchantId = $merchantId;
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function calculate(TransactionID $transactionId, Money $amount, int $status): Signature
    {
        return new Signature(strtoupper(md5(implode([
            $this->merchantId->getValue(),
            $transactionId,
            strtoupper(md5(strval($this->secretWord))),
            $this->moneyFormatter->format($amount),
            $amount->getCurrency(),
            $status,
        ]))));
    }
}
