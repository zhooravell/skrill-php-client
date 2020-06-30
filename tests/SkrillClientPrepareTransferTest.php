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
use Skrill\Request\TransferRequest;
use GuzzleHttp\Handler\MockHandler;
use Skrill\ValueObject\Description;
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
 * Class SkrillClientPrepareTransferTest.
 */
class SkrillClientPrepareTransferTest extends TestCase
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
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     * @throws Exception
     */
    public function testPrepareTransferSuccess()
    {
        $client = new Client(['handler' => $this->successTransferMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new TransferRequest(
            new Email('test@test.com'),
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $sid = $client->prepareTransfer($request);

        self::assertEquals('5e281d1376d92ba789ca7f0583e045d4', $sid);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
     */
    public function testPrepareTransferFail()
    {
        $this->expectException(SkrillResponseException::class);

        $client = new Client(['handler' => $this->failTransferMockHandler]);
        $client = new SkrillClient($client, new Email('test@test.com'), new Password('q1234567'));

        $request = new TransferRequest(
            new Email('test@test.com'),
            $this->parser->parse('10', 'USD'),
            new Description('subj', 'text')
        );

        $client->prepareTransfer($request);
    }

    /**
     * @throws GuzzleException
     * @throws InvalidDescriptionException
     * @throws InvalidEmailException
     * @throws InvalidPasswordException
     * @throws SkrillException
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
        $client = $this->createMock(ClientInterface::class);

        $response = $this->createMock(ResponseInterface::class);
        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(
                '<?xml version="1.0" encoding="UTF-8"?><response><sid>5e281d1376d92ba789ca7f0583e045d4</sid></response>'
            );

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
                ]
            )
            ->willReturn($response);

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
    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new DecimalMoneyParser(new ISOCurrencies());

        $successBody = <<<'XML'
<?xml version="1.0" encoding="UTF-8"?>
<response>
    <sid>5e281d1376d92ba789ca7f0583e045d4</sid>
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
        <error_msg>MISSING_AMOUNT</error_msg>
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
