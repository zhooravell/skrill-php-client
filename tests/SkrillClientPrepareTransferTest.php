<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use Skrill\Request\TransferRequest;
use Skrill\ValueObject\Description;
use Money\Parser\DecimalMoneyParser;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SkrillClientPrepareTransferTest.
 */
class SkrillClientPrepareTransferTest extends TestCase
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
     * @throws \Skrill\Exception\InvalidDescriptionException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     * @throws \Exception
     */
    public function testPrepareTransferSuccess()
    {
        $client = new Client(['handler' => $this->successSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new TransferRequest(
            new Email('test@test.com'),
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $sid = $client->prepareTransfer($request);

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', (string) $sid);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidDescriptionException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareTransferFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new TransferRequest(
            new Email('test@test.com'),
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $client->prepareTransfer($request);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidDescriptionException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testPrepareTransferCheckFormParams()
    {
        $email = 'test@test.com';
        $bnfEmail = 'test2@test.com';
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
                    'bnf_email' => $bnfEmail,
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

        $request = new TransferRequest(
            new Email($bnfEmail),
            $this->parser->parse(strval($amount), $currency),
            new Description($subject, $note)
        );

        $client = new SkrillClient($client, new Email($email), new Password($password));

        $client->prepareTransfer($request);
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
