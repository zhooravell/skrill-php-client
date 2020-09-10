<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Skrill\ValueObject\Email;
use Skrill\Factory\SidFactory;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use GuzzleHttp\Handler\MockHandler;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\Exception\SkrillException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidSidException;
use Skrill\Exception\InvalidEmailException;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class SkrillClientExecuteTransferTest.
 */
class SkrillClientExecuteTransferTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successTransferMockHandler;

    /**
     * @var HandlerStack
     */
    private $failTransferMockHandler;

    /**
     * @var DecimalMoneyParser
     */
    private $parser;

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteTransferSuccess()
    {
        $client = new Client(['handler' => $this->successTransferMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $result = $client->executeTransfer(SidFactory::createFromString('test-sid'));

        self::assertSame('2451071245', $result->get('id'));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteTransferFail()
    {
        $this->expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failTransferMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->executeTransfer(SidFactory::createFromString('test-sid'));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteTransferCheckFormParams()
    {
        $email = 'test@test.com';
        $password = 'q1234567';
        $sid = 'sid';

        /** @var ClientInterface $client */
        $client = $this->createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);

        $contents = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <transaction>
        <amount>0.50</amount>
        <currency>USD</currency>
        <id>2451071245</id>
        <status>2</status>
        <status_msg>processed</status_msg>
    </transaction>
</response>
XML;

        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn($contents);

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $client
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                'https://www.skrill.com/app/pay.pl',
                [
                    'form_params' => [
                        'action' => 'transfer',
                        'sid' => $sid,
                    ],
                    'headers' => [
                        'Accept' => 'text/xml',
                    ],
                ]
            )
            ->willReturn($response);

        $client = new SkrillClient($client, new Email($email), new Password($password));

        $client->executeTransfer(SidFactory::createFromString($sid));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());

        $successBody = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<response>
    <transaction>
        <amount>0.50</amount>
        <currency>USD</currency>
        <id>2451071245</id>
        <status>2</status>
        <status_msg>processed</status_msg>
    </transaction>
</response>
XML;

        $this->successTransferMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(200, [], $successBody),
                ]
            )
        );

        $failBody = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <error>
        <error_msg>SESSION_EXPIRED</error_msg>
    </error>
</response>
XML;

        $this->failTransferMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(200, [], $failBody),
                ]
            )
        );
    }
}
