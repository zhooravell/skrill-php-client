<?php

declare(strict_types=1);

namespace Skrill\Tests;

use Exception;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use GuzzleHttp\Handler\MockHandler;
use Skrill\Request\OnDemandRequest;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\Exception\SkrillException;
use Skrill\ValueObject\TransactionID;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\ValueObject\RecurringPaymentID;
use Skrill\Exception\InvalidEmailException;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;
use Skrill\Exception\InvalidTransactionIDException;
use Skrill\Exception\InvalidRecurringPaymentIDException;

/**
 * Class SkrillClientPrepareOnDemandTest.
 */
class SkrillClientPrepareOnDemandTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successOnDemandMockHandler;

    /**
     * @var HandlerStack
     */
    private $failOnDemandMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidRecurringPaymentIDException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     * @throws Exception
     */
    public function testPrepareOnDemandSuccess()
    {
        $client = new Client(['handler' => $this->successOnDemandMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $sid = $client->prepareOnDemand(new OnDemandRequest(
            new RecurringPaymentID('111'),
            new TransactionID(22),
            $this->parser->parse('10', 'EUR')
        ));

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidRecurringPaymentIDException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     */
    public function testPrepareOnDemandFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failOnDemandMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->prepareOnDemand(new OnDemandRequest(
            new RecurringPaymentID('111'),
            new TransactionID(22),
            $this->parser->parse('10', 'EUR')
        ));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidRecurringPaymentIDException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     */
    public function testPrepareOnDemandCheckFormParams()
    {
        $email = 'test@test.com';
        $currency = 'EUR';
        $amount = 10.55;
        $password = 'q1234567';
        $recurringPaymentId = '111';
        $transactionId = 222;

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
            ->with('POST', 'https://www.skrill.com/app/ondemand_request.pl', [
                'form_params' => [
                    'action' => 'prepare',
                    'frn_trn_id' => $transactionId,
                    'currency' => $currency,
                    'amount' => $amount,
                    'rec_payment_id' => $recurringPaymentId,
                    'email' => $email,
                    'password' => md5($password),
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $request = new OnDemandRequest(
            new RecurringPaymentID($recurringPaymentId),
            new TransactionID($transactionId),
            $this->parser->parse(strval($amount), 'EUR')
        );

        $client = new SkrillClient($client, new Email($email), new Password($password));
        $client->prepareOnDemand($request);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());
        $this->successOnDemandMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>'
            ),
        ]));

        $this->failOnDemandMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><error><error_msg>MISSING_AMOUNT</error_msg></error></response>'
            ),
        ]));
    }
}
