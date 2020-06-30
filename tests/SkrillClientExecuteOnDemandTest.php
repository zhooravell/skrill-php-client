<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use GuzzleHttp\Psr7\Response;
use Skrill\Factory\SidFactory;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\ClientInterface;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use GuzzleHttp\Handler\MockHandler;
use Money\Parser\DecimalMoneyParser;
use Skrill\Exception\SkrillException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Skrill\Exception\InvalidSidException;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidEmailException;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class SkrillClientExecuteOnDemandTest.
 */
class SkrillClientExecuteOnDemandTest extends TestCase
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
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteOnDemandSuccess()
    {
        $client = new Client(['handler' => $this->successOnDemandMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $result = $client->executeOnDemand(SidFactory::createFromString('test-sid'));

        self::assertEquals('2451071245', $result->get('id'));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteOnDemandFail()
    {
        $this->expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failOnDemandMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->executeOnDemand(SidFactory::createFromString('test-sid'));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws InvalidSidException
     * @throws SkrillException
     */
    public function testExecuteOnDemandCheckFormParams()
    {
        $email = 'test@test.com';
        $password = 'q1234567';
        $sid = 'sid';

        /** @var ClientInterface|MockObject $client */
        $client = $this->createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(
                '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
                        <response>
                            <transaction>
                                <amount>0.50</amount>
                                <currency>USD</currency>
                                <id>2451071245</id>
                                <status>2</status>
                                <status_msg>processed</status_msg>
                            </transaction>
                        </response>'
            );

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $client
            ->expects(self::once())
            ->method('request')
            ->with(
                'POST',
                'https://www.skrill.com/app/ondemand_request.pl',
                [
                    'form_params' => [
                        'action' => 'request',
                        'sid' => $sid,
                    ],
                    'headers' => [
                        'Accept' => 'text/xml',
                    ],
                ]
            )
            ->willReturn($response);

        $client = new SkrillClient($client, new Email($email), new Password($password));

        $client->executeOnDemand(SidFactory::createFromString($sid));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
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

        $this->successOnDemandMockHandler = HandlerStack::create(
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

        $this->failOnDemandMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(200, [], $failBody),
                ]
            )
        );
    }
}
