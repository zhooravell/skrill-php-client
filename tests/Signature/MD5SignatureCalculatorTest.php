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
use Skrill\Signature\MD5SignatureCalculator;
use Skrill\Exception\InvalidSignatureException;
use Skrill\Exception\InvalidSecretWordException;
use Skrill\Exception\InvalidTransactionIDException;

/**
 * Class MD5SignatureCalculatorTest.
 */
class MD5SignatureCalculatorTest extends TestCase
{
    /**
     * @throws InvalidSecretWordException
     * @throws InvalidSignatureException
     * @throws InvalidTransactionIDException
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
        $expected = 'CF9DCA614656D19772ECAB978A56866D';

        $calculator = new MD5SignatureCalculator($secretWord, $merchantId, $formatter);

        self::assertEquals($expected, (string) $calculator->calculate($transactionId, $amount, $status));
    }
}
