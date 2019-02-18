<?php

declare(strict_types=1);

namespace Skrill\Tests;

use GuzzleHttp\Client;
use Skrill\SkrillClient;
use GuzzleHttp\HandlerStack;
use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SkrillClientViewHistoryTest.
 */
class SkrillClientViewHistoryTest extends TestCase
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     * @throws \Exception
     */
    public function testViewHistorySuccess()
    {
        $client = new Client(['handler' => $this->successSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $history = $client->viewHistory(new \DateTime('2018-01-01'));

        self::assertInstanceOf(\ArrayObject::class, $history);
        self::assertCount(3, $history);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Skrill\Exception\InvalidEmailException
     * @throws \Skrill\Exception\InvalidPasswordException
     * @throws \Skrill\Exception\SkrillException
     */
    public function testViewHistoryFail()
    {
        self::expectException(SkrillResponseException::class);
        $this->expectExceptionMessage('Skrill error: Illegal parameter value: 02-08-201');

        $client = new Client(['handler' => $this->failSidMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $client->viewHistory(new \DateTime('2018-01-01'), new \DateTime('2018-02-01'));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->successSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '"ID","Time (CET)","Type","Transaction Details","[-] USD","[+] USD","Status","balance","Reference","Amount Sent","Currency sent","More information","ID of the corresponding Skrill transaction","Payment Type"
"2450853010","02 Aug 18 09:55","Send Money","to nkoptel@centrobill.com",".5","","processed","558.513759","","0.5","USD","Test trasfer subject","2450853001","WLT"
"2450853011","02 Aug 18 09:55","Send Money","Fee",".01","","processed","558.503759","","","","","2450853001",""
"2450870079","02 Aug 18 10:21","Send Money","to nkoptel@centrobill.com",".5","","processed","558.003759","","0.5","USD","Test trasfer subject","2450870067","WLT"'
            ),
        ]));

        $this->failSidMockHandler = HandlerStack::create(new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(
                200,
                [],
                '404		Illegal parameter value: 02-08-201'
            ),
        ]));
    }
}
