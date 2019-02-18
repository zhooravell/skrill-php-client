<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use Skrill\ValueObject\Url;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use Skrill\Request\SaleRequest;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Language;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use Skrill\ValueObject\Description;
use Skrill\ValueObject\CompanyName;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Psr\Http\Message\StreamInterface;
use Skrill\SkrillSaleClientInterface;
use Skrill\SkrillRefundClientInterface;
use Psr\Http\Message\ResponseInterface;
use Skrill\SkrillHistoryClientInterface;
use Skrill\SkrillOnDemandClientInterface;
use Skrill\SkrillTransferClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SkrillClientTest.
 */
class SkrillClientPrepareSaleTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successSidMockHandler;

    /**
     * @var HandlerStack
     */
    private $failSidMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     */
    public function testImplementation()
    {
        self::assertInstanceOf(SkrillHistoryClientInterface::class, new SkrillClient(new Client(), new Email('test@test.com'), new Password('q1234567')));
        self::assertInstanceOf(SkrillOnDemandClientInterface::class, new SkrillClient(new Client(), new Email('test@test.com'), new Password('q1234567')));
        self::assertInstanceOf(SkrillSaleClientInterface::class, new SkrillClient(new Client(), new Email('test@test.com'), new Password('q1234567')));
        self::assertInstanceOf(SkrillTransferClientInterface::class, new SkrillClient(new Client(), new Email('test@test.com'), new Password('q1234567')));
        self::assertInstanceOf(SkrillRefundClientInterface::class, new SkrillClient(new Client(), new Email('test@test.com'), new Password('q1234567')));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     * @throws \Exception
     */
    public function testPrepareSaleSuccess()
    {
        $client = new Client(['handler' => $this->successSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $transactionId = new TransactionID('123');
        $amount = $this->parser->parse('10.5', 'EUR');

        $request = new SaleRequest($transactionId, $amount);

        $sid = $client->prepareSale($request);

        $now = new \DateTime();
        $diff = $now->diff($sid->getExpirationTillDateTime());

        self::assertEquals(14, $diff->i);
        self::assertEquals(59, $diff->s);
        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareSaleFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $transactionId = new TransactionID('123');
        $amount = $this->parser->parse('10.5', 'EUR');

        $request = new SaleRequest($transactionId, $amount);

        $client->prepareSale($request);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareSaleCheckFormParams()
    {
        $email = 'test@test.com';
        $currency = 'EUR';
        $amount = 10.55;
        $transactionId = new TransactionID('123');

        /** @var ClientInterface|MockObject $client */
        $client = self::createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn('<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>');

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $client
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'https://pay.skrill.com', [
                'form_params' => [
                    'transaction_id' => (string) $transactionId,
                    'currency' => $currency,
                    'amount' => $amount,
                    'prepare_only' => 1,
                    'pay_to_email' => $email,
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $client = new SkrillClient($client, new Email($email), new Password('q1234567'));
        $request = new SaleRequest($transactionId, $this->parser->parse(strval($amount), 'EUR'));

        $client->prepareSale($request);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidCompanyNameException
     * @throws \Skrill\Exception\InvalidDescriptionException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidLangException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidUrlException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareSaleCheckFormParams2()
    {
        $email = 'test@test.com';
        $currency = 'EUR';
        $amount = 10.55;
        $company = 'TEST Company';
        $lang = 'BG';
        $customerEmail = 'customer@test.com';
        $desc = 'Product ID:';
        $text = '4509334';
        $transactionId = new TransactionID('123');

        /** @var ClientInterface|MockObject $client */
        $client = self::createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn('<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>');

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $client
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'https://pay.skrill.com', [
                'form_params' => [
                    'transaction_id' => (string) $transactionId,
                    'currency' => $currency,
                    'amount' => $amount,
                    'prepare_only' => 1,
                    'pay_to_email' => $email,
                    'recipient_description' => $company,
                    'language' => $lang,
                    'pay_from_email' => $customerEmail,
                    'detail1_description' => 'Product ID:',
                    'detail1_text' => '4509334',
                    'logo_url' => 'https://google.com',
                    'status_url' => 'https://google.com/3',
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $client = new SkrillClient($client, new Email($email), new Password('q1234567'), new Url('https://google.com'), new CompanyName($company));
        $request = new SaleRequest($transactionId, $this->parser->parse(strval($amount), 'EUR'));
        $request
            ->setLang(new Language($lang))
            ->setPayFromEmail(new Email($customerEmail))
            ->setProductDescription(new Description($desc, $text))
            ->setStatusUrl(new Url('https://google.com/3'))
        ;

        $client->prepareSale($request);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());

        $this->successSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '5e281d1376d92ba789ca7f0583e045d4'
            ),
        ]));

        $this->failSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '{"code":"BAD_REQUEST","message":"Already paid for 2451748842"}'
            ),
        ]));
    }
}
