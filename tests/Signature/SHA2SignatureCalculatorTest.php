<?php

declare(strict_types=1);

namespace Skrill\Tests\Signature;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\MerchantID;
use Skrill\ValueObject\SecretWord;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Money\Formatter\DecimalMoneyFormatter;
use Skrill\Signature\SHA2SignatureCalculator;

/**
 * Class SHA2SignatureCalculatorTest.
 */
class SHA2SignatureCalculatorTest extends TestCase
{
    /**
     * @throws \Skrill\Exception\InvalidSecretWordException
     * @throws \Skrill\Exception\InvalidSignatureException
     * @throws \Skrill\Exception\InvalidTransactionIdException
     */
    public function testCalculateMethod()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());
        $formatter = new DecimalMoneyFormatter(new ISOCurrencies());

        $transactionId = new TransactionID(5585262);
        $secretWord = new SecretWord('Answer One');
        $merchantId = new MerchantID(4637827);
        $amount = $parser->parse('9.99', 'EUR');
        $status = 2;
        $expected = '216AAA4C0F59643C16D7655C56EEB86E832D61F136C1EF11335DA808FFE6C096';

        $calculator = new SHA2SignatureCalculator($secretWord, $merchantId, $formatter);

        self::assertEquals($expected, (string) $calculator->calculate($transactionId, $amount, $status));
    }
}
