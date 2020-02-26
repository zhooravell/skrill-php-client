<?php

declare(strict_types=1);

namespace Skrill\Tests;

use Exception;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use Skrill\Request\PayoutRequest;
use GuzzleHttp\Handler\MockHandler;
use Skrill\ValueObject\Description;
use Skrill\ValueObject\TransactionId;
use Money\Parser\DecimalMoneyParser;
use Psr\Http\Message\StreamInterface;
use Skrill\Exception\SkrillException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidEmailException;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;
use Skrill\Exception\InvalidDescriptionException;

/**
 * Class SkrillClientPreparePayoutTest.
 */
class SkrillClientPreparePayoutTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successPayoutMockHandler;

    /**
     * @var HandlerStack
     */
    private $failPayoutMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws GuzzleException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     * @throws Exception
     */
    public function testPreparePayoutSuccess()
    {
        $client = new Client(['handler' => $this->successPayoutMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new PayoutRequest(
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $sid = $client->preparePayout($request);

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testPreparePayoutFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failPayoutMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new PayoutRequest(
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $client->preparePayout($request);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testPreparePayoutCheckFormParams()
    {
        $email = 'test@test.com';
        $transactionId = '12321';
        $currency = 'EUR';
        $amount = 10.55;
        $password = 'q1234567';
        $subject = 'Subject';
        $note = 'Note';

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
            ->with('POST', 'https://www.skrill.com/app/pay.pl', [
                'form_params' => [
                    'action' => 'prepare',
                    'mb_transaction_id' => $transactionId,
                    'currency' => $currency,
                    'amount' => $amount,
                    'subject' => $subject,
                    'note' => $note,
                    'email' => $email,
                    'password' => md5($password),
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $request = new PayoutRequest(
            $this->parser->parse(strval($amount), $currency),
            new Description($subject, $note)
        );

        $request->setOriginalTransactionId(new TransactionId($transactionId));

        $client = new SkrillClient($client, new Email($email), new Password($password));

        $client->preparePayout($request);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());
        $this->successPayoutMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>'
            ),
        ]));

        $this->failPayoutMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><error><error_msg>MISSING_AMOUNT</error_msg></error></response>'
            ),
        ]));
    }
}
