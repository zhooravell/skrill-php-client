<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use Skrill\Factory\SidFactory;
use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SkrillClientExecuteRefundTest.
 */
class SkrillClientExecuteRefundTest extends TestCase
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
     * @throws \Skrill\Exception\InvalidSidException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testExecuteRefundSuccess()
    {
        $client = new Client(['handler' => $this->successSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $result = $client->executeRefund(SidFactory::createFromString('test-sid'));

        self::assertEquals('e40a8e22-016e-4687-870c-f073631e3131', $result->get('transaction_id'));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidSidException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testExecuteRefundFail()
    {
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->executeRefund(SidFactory::createFromString('test-sid'));
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\InvalidSidException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testExecuteRefundCheckFormParams()
    {
        $email = 'test@test.com';
        $password = 'q1234567';
        $sid = 'sid';

        /** @var ClientInterface $client */
        $client = self::createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(
                '<?xml version="1.0" encoding="UTF-8"?>
                        <response>
                            <mb_amount>1.000000</mb_amount>
                            <mb_currency>USD</mb_currency>
                            <mb_transaction_id>2451682729</mb_transaction_id>
                            <merchant_id>105701451</merchant_id>
                            <status>2</status>
                            <transaction_id>e40a8e22-016e-4687-870c-f073631e3131</transaction_id>
                        </response>'
            );

        $response->expects(self::once())
            ->method('getBody')
            ->willReturn($responseBody);

        $client
            ->expects(self::once())
            ->method('request')
            ->with('POST', 'https://www.skrill.com/app/refund.pl', [
                'form_params' => [
                    'action' => 'refund',
                    'sid' => $sid,
                ],
                'headers' => [
                    'Accept' => 'text/xml',
                ],
            ])
            ->willReturn($response)
        ;

        $client = new SkrillClient($client, new Email($email), new Password($password));

        $client->executeRefund(SidFactory::createFromString($sid));
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
                '<?xml version="1.0" encoding="UTF-8"?>
                        <response>
                            <mb_amount>1.000000</mb_amount>
                            <mb_currency>USD</mb_currency>
                            <mb_transaction_id>2451682729</mb_transaction_id>
                            <merchant_id>105701451</merchant_id>
                            <status>2</status>
                            <transaction_id>e40a8e22-016e-4687-870c-f073631e3131</transaction_id>
                        </response>'
            ),
        ]));

        $this->failSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '<?xml version="1.0" encoding="UTF-8"?><response><error><error_msg>SESSION_EXPIRED</error_msg></error></response>'
            ),
        ]));
    }
}
