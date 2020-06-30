<?php

declare(strict_types=1);

namespace Skrill\Tests;

use DateTime;
use Exception;
use ArrayObject;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use GuzzleHttp\Handler\MockHandler;
use Skrill\Exception\SkrillException;
use GuzzleHttp\Exception\GuzzleException;
use Skrill\Exception\InvalidEmailException;
use Skrill\Exception\SkrillResponseException;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class SkrillClientViewHistoryTest.
 */
class SkrillClientViewHistoryTest extends TestCase
{
    /**
     * @var HandlerStack
     */
    private $successHistoryMockHandler;

    /**
     * @var HandlerStack
     */
    private $failHistoryMockHandler;

    /**
     * @var HandlerStack
     */
    private $invalidTimeHistoryMockHandler;

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     * @throws Exception
     */
    public function testViewHistorySuccess()
    {
        $container = [];
        $history = Middleware::history($container);
        $handlerStack = HandlerStack::create($this->successHistoryMockHandler);
        $handlerStack->push($history);

        $client = new Client(['handler' => $handlerStack]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));
        $history = $client->viewHistory(new DateTime('2018-01-01'));

        self::assertInstanceOf(ArrayObject::class, $history);
        self::assertCount(3, $history);
        self::assertCount(1, $container); // should be one request
        /** @var Request $request */
        $request = $container[0]['request'];
        self::assertInstanceOf(Request::class, $request);
        self::assertEquals('POST', $request->getMethod());
        self::assertEquals('https://www.skrill.com/app/query.pl', $request->getUri());
        self::assertEquals(
            'email=test%40test.com&password=3ade3fd6e8eef84f2ea91f6474be10d9&action=history&start_date=01-01-2018',
            $request->getBody()->getContents()
        );
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testViewHistoryFail()
    {
        $this->expectException(SkrillResponseException::class);
        $this->expectExceptionMessage('Skrill error: Illegal parameter value: 02-08-201');

        $client = new Client(['handler' => $this->failHistoryMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->viewHistory(new DateTime('2018-01-01'), new DateTime('2018-02-01'));
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testViewHistoryInvalidTime()
    {
        $this->expectException(SkrillResponseException::class);
        $this->expectExceptionMessage('Skrill error: Invalid time "TEST".');

        $client = new Client(['handler' => $this->invalidTimeHistoryMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->viewHistory(new DateTime('2018-01-01'), new DateTime('2018-02-01'));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->successHistoryMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(
                        200,
                        [],
                        file_get_contents(__DIR__ . '/DataFixtures/success-history-body.csv')
                    ),
                ]
            )
        );

        $this->invalidTimeHistoryMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(
                        200,
                        [],
                        file_get_contents(__DIR__ . '/DataFixtures/invalid-time-history-body.csv')
                    ),
                ]
            )
        );

        $this->failHistoryMockHandler = HandlerStack::create(
            new MockHandler(
                [
                    new Response(
                        200,
                        [],
                        '404		Illegal parameter value: 02-08-201'
                    ),
                ]
            )
        );
    }
}
