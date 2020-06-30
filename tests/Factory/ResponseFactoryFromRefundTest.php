<?php

namespace Skrill\Tests\Factory;

use PHPUnit\Framework\TestCase;
use Skrill\Factory\ResponseFactory;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Skrill\Exception\SkrillResponseException;

/**
 * Class ResponseFactoryFromRefundTest.
 */
class ResponseFactoryFromRefundTest extends TestCase
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
     * @throws SkrillResponseException
     */
    public function testSuccess()
    {
        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/success_refund.xml'));

        $response = ResponseFactory::createFromRefundResponse($this->response);

        self::assertEquals(1, $response->get('mb_amount'));
        self::assertEquals('USD', $response->get('mb_currency'));
        self::assertEquals('e40a8e22-016e-4687-870c-f073631e3131', $response->get('transaction_id'));
        self::assertEquals(2, $response->get('status'));
        self::assertNull($response->get('error'));
    }

    /**
     * @throws SkrillResponseException
     */
    public function testError()
    {
        self::expectException(SkrillResponseException::class);
        self::expectExceptionMessage('Skrill error: MISSING_AMOUNT');

        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/error.xml'));

        ResponseFactory::createFromRefundResponse($this->response);
    }

    /**
     * @throws SkrillResponseException
     */
    public function testResponseInvalidTransactionFormat()
    {
        self::expectException(SkrillResponseException::class);
        self::expectExceptionMessage('Skrill invalid response format with transaction.');

        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/invalid_refund_format.xml'));

        ResponseFactory::createFromRefundResponse($this->response);
    }

    /**
     * @throws SkrillResponseException
     */
    public function testSuccessWithError()
    {
        $this->responseBody->expects(self::once())
            ->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/DataFixtures/refund_with_error.xml'));

        $response = ResponseFactory::createFromRefundResponse($this->response);

        self::assertEquals(1, $response->get('mb_amount'));
        self::assertEquals('USD', $response->get('mb_currency'));
        self::assertEquals('e40a8e22-016e-4687-870c-f073631e3131', $response->get('transaction_id'));
        self::assertEquals(2, $response->get('status'));
        self::assertEquals('ALREADY_REFUNDED', $response->get('error'));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseBody = $this->createMock(StreamInterface::class);

        $this->response->expects(self::once())
            ->method('getBody')
            ->willReturn($this->responseBody);
    }
}
