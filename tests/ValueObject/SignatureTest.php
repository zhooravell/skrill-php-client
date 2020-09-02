<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\Signature;
use Skrill\Exception\InvalidSignatureException;

/**
 * Class SignatureTest.
 */
class SignatureTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidSignatureException
     */
    public function testSuccess()
    {
        $value = strtoupper(md5('test123'));
        $signature = new Signature($value);

        self::assertSame($value, (string)$signature);
        self::assertTrue($signature->equalToString($value));
        self::assertFalse($signature->equalToString('test'));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidSignatureException
     */
    public function testEmptyValue(string $value)
    {
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Signature should not be blank.');

        new Signature($value);
    }

    /**
     * @throws InvalidSignatureException
     */
    public function testLowercase()
    {
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Signature should in uppercase.');

        new Signature(md5('test123'));
    }
}
