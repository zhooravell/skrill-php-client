<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Signature;
use Skrill\Exception\InvalidSignatureException;

/**
 * Class SignatureTest.
 */
class SignatureTest extends TestCase
{
    /**
     * @throws InvalidSignatureException
     */
    public function testSuccess()
    {
        $value = strtoupper(md5('test123'));
        $signature = new Signature($value);

        self::assertEquals($value, (string) $signature);
        self::assertTrue($signature->equalToString($value));
        self::assertFalse($signature->equalToString('test'));
    }

    /**
     * @throws InvalidSignatureException
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidSignatureException::class);
        self::expectExceptionMessage('Signature should not be blank.');

        new Signature(' ');
    }

    /**
     * @throws InvalidSignatureException
     */
    public function testLowercase()
    {
        self::expectException(InvalidSignatureException::class);
        self::expectExceptionMessage('Signature should in uppercase.');

        new Signature(md5('test123'));
    }
}
