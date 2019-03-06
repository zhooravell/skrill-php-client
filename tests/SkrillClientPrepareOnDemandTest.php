<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use Skrill\Request\OnDemandRequest;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\ValueObject\TransactionID;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Skrill\ValueObject\RecurringPaymentID;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SkrillClientPrepareOnDemandTest.
 */
class SkrillClientPrepareOnDemandTest extends TestCase
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidRecurringPaymentIdException
     * @throws \Skrill\Exception\InvalidTransactionIdException
     * @throws \Skrill\Exception\SkrillException
     * @throws \Exception
     */
    public function testPrepareOnDemandSuccess()
    {
        $client = new Client(['handler' => $this->successSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new OnDemandRequest(
            new RecurringPaymentID('111'),
            new TransactionID(22),
            $this->parser->parse('10', 'EUR')
        );

        $sid = $client->prepareOnDemand($request);

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidRecurringPaymentIdException
     * @throws \Skrill\Exception\InvalidTransactionIdException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareOnDemandFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new OnDemandRequest(
            new RecurringPaymentID('111'),
            new TransactionID(22),
            $this->parser->parse('10', 'EUR')
        );

        $client->prepareOnDemand($request);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidRecurringPaymentIdException
     * @throws \Skrill\Exception\InvalidTransactionIdException
     * @throws \Skrill\Exception\SkrillException
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

        $this->successSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>'
            ),
        ]));

        $this->failSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><error><error_msg>MISSING_AMOUNT</error_msg></error></response>'
            ),
        ]));
    }
}
