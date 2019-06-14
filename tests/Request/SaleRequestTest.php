<?php

declare(strict_types=1);

namespace Skrill\Tests\Request;

use Skrill\ValueObject\Url;
use Skrill\ValueObject\Email;
use Skrill\Request\SaleRequest;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Language;
use Money\Currencies\ISOCurrencies;
use Skrill\ValueObject\Description;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Skrill\Exception\InvalidUrlException;
use Skrill\Exception\InvalidLangException;
use Skrill\Exception\InvalidEmailException;
use Skrill\ValueObject\RecurringBillingNote;
use Skrill\Exception\InvalidDescriptionException;
use Skrill\Exception\InvalidTransactionIDException;
use Skrill\Exception\InvalidRecurringBillingNoteException;

/**
 * Class RedirectUrlRequestTest.
 */
class SaleRequestTest extends TestCase
{
    /**
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidLangException
     * @throws InvalidRecurringBillingNoteException
     * @throws InvalidTransactionIDException
     * @throws InvalidUrlException
     */
    public function testSuccess()
    {
        $parser = new DecimalMoneyParser(new ISOCurrencies());
        $request = new SaleRequest(new TransactionID(111), $parser->parse('10', 'EUR'));

        self::assertEquals(
            [
                'transaction_id' => '111',
                'currency' => 'EUR',
                'amount' => 10.0,
            ],
            $request->getPayload()
        );

        $res = $request
            ->setLang(new Language('DA'))
            ->setPayFromEmail(new Email('test@test.com'))
            ->setProductDescription(new Description('Product ID:', '111'))
            ->setReturnUrl(new Url('https://google.com/1'))
            ->setCancelUrl(new Url('https://google.com/2'))
            ->setStatusUrl(new Url('https://google.com/3'))
            ->enableRecurringBilling(new RecurringBillingNote('Recurring billing'), $parser->parse('12', 'EUR'))
        ;

        self::assertInstanceOf(SaleRequest::class, $res);
        self::assertEquals(
            [
                'transaction_id' => '111',
                'currency' => 'EUR',
                'amount' => 10.0,
                'language' => 'DA',
                'pay_from_email' => 'test@test.com',
                'detail1_description' => 'Product ID:',
                'detail1_text' => '111',
                'return_url' => 'https://google.com/1',
                'cancel_url' => 'https://google.com/2',
                'status_url' => 'https://google.com/3',
                'ondemand_max_amount' => 12.0,
                'ondemand_max_currency' => 'EUR',
                'ondemand_note' => 'Recurring billing',
            ],
            $request->getPayload()
        );
    }
}
