<?php

declare(strict_types=1);

namespace Skrill\Tests;

use DateTime;
use Exception;
use ArrayObject;
use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
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
        $client = new Client(['handler' => $this->successHistoryMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));
        $history = $client->viewHistory(new DateTime('2018-01-01'));

        self::assertInstanceOf(ArrayObject::class, $history);
        self::assertCount(3, $history);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testViewHistoryFail()
    {
        self::expectException(SkrillResponseException::class);
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
        self::expectException(SkrillResponseException::class);
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

        $this->successHistoryMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '"ID","Time (CET)","Type","Transaction Details","[-] USD","[+] USD","Status","balance","Reference","Amount Sent","Currency sent","More information","ID of the corresponding Skrill transaction","Payment Type"
"2450853010","02 Aug 18 09:55","Send Money","to nkoptel@centrobill.com",".5","","processed","558.513759","","0.5","USD","Test trasfer subject","2450853001","WLT"
"2450853011","02 Aug 18 09:55","Send Money","Fee",".01","","processed","558.503759","","","","","2450853001",""
"2450870079","02 Aug 18 10:21","Send Money","to nkoptel@centrobill.com",".5","","processed","558.003759","","0.5","USD","Test trasfer subject","2450870067","WLT"'
            ),
        ]));

        $this->invalidTimeHistoryMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '"ID","Time (CET)","Type","Transaction Details","[-] USD","[+] USD","Status","balance","Reference","Amount Sent","Currency sent","More information","ID of the corresponding Skrill transaction","Payment Type"
"2450853010","02 Aug 18 09:55","Send Money","to nkoptel@centrobill.com",".5","","processed","558.513759","","0.5","USD","Test trasfer subject","2450853001","WLT"
"2450853011","02 Aug 18 09:55","Send Money","Fee",".01","","processed","558.503759","","","","","2450853001",""
"2450870079","TEST","Send Money","to nkoptel@centrobill.com",".5","","processed","558.003759","","0.5","USD","Test trasfer subject","2450870067","WLT"'
            ),
        ]));

        $this->failHistoryMockHandler = HandlerStack::create(new MockHandler([
            new Response(
                200,
                [],
                '404		Illegal parameter value: 02-08-201'
            ),
        ]));
    }
}
