<?php

namespace Skrill\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Skrill\Factory\ResponseFactory;
use Skrill\Exception\SkrillException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;

/**
 * Class ResponseFactoryFromTransferTest.
 */
class ResponseFactoryFromTransferTest extends TestCase
{
    /**
     * @var ResponseInterface|MockObject
     */
    private $response;

    /**
     * @var StreamInterface|MockObject
     */
    private $responseBody;

    /**
     * @throws SkrillException
     */
    public function testSuccess()
    {
        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/success_transaction.xml'));

        $response = ResponseFactory::createFromTransferResponse($this->response);

        self::assertEquals(0.50, $response->get('amount'));
        self::assertSame('USD', $response->get('currency'));
        self::assertSame('2451071245', $response->get('id'));
        self::assertSame('2', $response->get('status'));
        self::assertSame('processed', $response->get('status_msg'));
    }

    /**
     * @throws SkrillException
     */
    public function testError()
    {
        self::expectException(SkrillResponseException::class);
        self::expectExceptionMessage('Skrill error: MISSING_AMOUNT');

        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/error.xml'));

        ResponseFactory::createFromTransferResponse($this->response);
    }

    /**
     * @throws SkrillException
     */
    public function testResponseInvalidTransactionFormat()
    {
        self::expectException(SkrillResponseException::class);
        self::expectExceptionMessage('Skrill invalid response format with transaction.');

        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/invalid_transaction_format.xml'));

        ResponseFactory::createFromTransferResponse($this->response);
    }

    /**
     * @throws SkrillException
     */
    public function testSuccessWithError()
    {
        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/transaction_with_error.xml'));

        $response = ResponseFactory::createFromTransferResponse($this->response);

        self::assertEquals(0.50, $response->get('amount'));
        self::assertSame('USD', $response->get('currency'));
        self::assertSame('2451071245', $response->get('id'));
        self::assertSame('-2', $response->get('status'));
        self::assertSame('BALANCE_NOT_ENOUGH', $response->get('error_msg'));
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseBody = $this->createMock(StreamInterface::class);

        $this->response->expects(self::once())
            ->method('getBody')
            ->willReturn($this->responseBody);
    }
}
