<?php

declare(strict_types=1);

namespace Skrill\Tests;

use Exception;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use Skrill\ValueObject\Url;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use GuzzleHttp\Psr7\Response;
use Skrill\Request\SaleRequest;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Language;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use Skrill\ValueObject\Description;
use GuzzleHttp\Handler\MockHandler;
use Skrill\ValueObject\CompanyName;
use Money\Parser\DecimalMoneyParser;
use Skrill\Exception\SkrillException;
use Skrill\ValueObject\TransactionID;
use Psr\Http\Message\StreamInterface;
use Skrill\SkrillSaleClientInterface;
use Skrill\SkrillRefundClientInterface;
use Psr\Http\Message\ResponseInterface;
use Skrill\SkrillHistoryClientInterface;
use Skrill\Exception\InvalidUrlException;
use Skrill\SkrillOnDemandClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\SkrillTransferClientInterface;
use Skrill\Exception\InvalidLangException;
use Skrill\Exception\InvalidEmailException;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;
use Skrill\Exception\InvalidCompanyNameException;
use Skrill\Exception\InvalidDescriptionException;

/**
 * Class SkrillClientTest.
 */
class SkrillClientPrepareSaleTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successSaleMockHandler;

    /**
     * @var HandlerStack
     */
    private $failSaleMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
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
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     * @throws Exception
     */
    public function testPrepareSaleSuccess()
    {
        $client = new Client(['handler' => $this->successSaleMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $sid = $client->prepareSale(new SaleRequest(
                new TransactionID('123'),
                $this->parser->parse('10.5', 'EUR')
            )
        );

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testPrepareSaleFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failSaleMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new SaleRequest(
            new TransactionID('123'),
            $this->parser->parse('10.5', 'EUR')
        );

        $client->prepareSale($request);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
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
     * @throws GuzzleException
     * @throws InvalidCompanyNameException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidLangException
     * @throws InvalidPasswordException
     * @throws InvalidUrlException
     * @throws SkrillException
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
        $this->successSaleMockHandler = HandlerStack::create(new MockHandler([
            new Response(200, [], '5e281d1376d92ba789ca7f0583e045d4'),
        ]));
        $this->failSaleMockHandler = HandlerStack::create(new MockHandler([
            new Response(200, [], '{"code":"BAD_REQUEST","message":"Already paid for 2451748842"}'
            ),
        ]));
    }
}
