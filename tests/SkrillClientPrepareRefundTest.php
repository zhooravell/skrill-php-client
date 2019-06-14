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
use Skrill\Request\RefundRequest;
use GuzzleHttp\Handler\MockHandler;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Psr\Http\Message\StreamInterface;
use Skrill\Exception\SkrillException;
use Skrill\ValueObject\TransactionID;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidEmailException;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;
use Skrill\Exception\InvalidTransactionIDException;

/**
 * Class SkrillClientPrepareRefundTest.
 */
class SkrillClientPrepareRefundTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successRefundMockHandler;

    /**
     * @var HandlerStack
     */
    private $failRefundMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     * @throws Exception
     */
    public function testPrepareTransferSuccess()
    {
        $client = new Client(['handler' => $this->successRefundMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $sid = $client->prepareRefund(new RefundRequest(new TransactionID('test')));

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     */
    public function testPrepareTransferFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failRefundMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->prepareRefund(new RefundRequest(new TransactionID('test')));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidTransactionIDException
     * @throws SkrillException
     */
    public function testPrepareTransferCheckFormParams()
    {
        $email = 'test@test.com';
        $transactionId = 'test';
        $password = 'q1234567';

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
            ->with('POST', 'https://www.skrill.com/app/refund.pl', [
                'form_params' => [
                    'action' => 'prepare',
                    'transaction_id' => $transactionId,
                    'email' => $email,
                    'password' => md5($password),
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $client = new SkrillClient($client, new Email($email), new Password($password));
        $client->prepareRefund(new RefundRequest(new TransactionID($transactionId)));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());
        $this->successRefundMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>'
            ),
        ]));

        $this->failRefundMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><error><error_msg>MISSING_AMOUNT</error_msg></error></response>'
            ),
        ]));
    }
}
