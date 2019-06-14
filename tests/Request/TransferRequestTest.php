<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use Money\Currencies\ISOCurrencies;
use Skrill\ValueObject\Description;
use Skrill\Request\TransferRequest;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidEmailException;
use Skrill\Exception\InvalidDescriptionException;
use Skrill\Exception\InvalidTransactionIDException;

/**
 * Class TransferRequestTest.
 */
class TransferRequestTest extends TestCase
{
    /**
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidTransactionIDException
     */
    public function testSuccess()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());

        $request = new TransferRequest(
            new Email('test@test.com'),
            $parser->parse('1000000.51', 'EUR'),
            new Description('Product ID:', '111')
        );

        self::assertEquals(
            [
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'bnf_email' => 'test@test.com',
                'subject' => 'Product ID:',
                'note' => '111',
            ],
            $request->getPayload()
        );

        self::assertInstanceOf(TransferRequest::class, $request->setReferenceTransaction(new TransactionID('test')));
        self::assertEquals(
            [
                'currency' => 'EUR',
                'amount' => 1000000.51,
                'bnf_email' => 'test@test.com',
                'subject' => 'Product ID:',
                'note' => '111',
                'frn_trn_id' => 'test',
            ],
            $request->getPayload()
        );
    }
}
