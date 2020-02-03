<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Skrill\ValueObject\Email;
use Skrill\Factory\SidFactory;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use GuzzleHttp\Handler\MockHandler;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Skrill\Exception\SkrillException;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidSidException;
use Skrill\Exception\InvalidEmailException;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class SkrillClientExecutePayoutTest
 */
class SkrillClientExecutePayoutTest extends TestCase
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

        $result = $client->executePayout(SidFactory::createFromString('test-sid'));

        self::assertEquals('2451071245', (string)$result->get('id'));
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
        self::expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failOnDemandMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->executePayout(SidFactory::createFromString('test-sid'));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $successXML = <<<'XML'
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

        $failedXML = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <error>
        <error_msg>SESSION_EXPIRED</error_msg>
    </error>
</response>
XML;

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());
        $this->successOnDemandMockHandler = HandlerStack::create(
            new MockHandler([new Response(200, [], $successXML)])
        );
        $this->failOnDemandMockHandler = HandlerStack::create(
            new MockHandler([new Response(200, [], $failedXML)])
        );
    }
}
