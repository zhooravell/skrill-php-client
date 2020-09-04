<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\CompanyName;
use Skrill\Exception\InvalidCompanyNameException;

/**
 * Class CompanyNameTest.
 */
class CompanyNameTest extends TestCase
{
    /**
     * @throws InvalidCompanyNameException
     */
    public function testSuccess()
    {
        $value = 'test123';
        $authKey = new CompanyName($value);

        self::assertSame($value, (string)$authKey);
    }

    /**
     * @throws InvalidCompanyNameException
     */
    public function testSuccess2()
    {
        $value = str_repeat('a', 30);
        $authKey = new CompanyName($value);

        self::assertSame($value, (string)$authKey);
    }

    /**
     * @throws InvalidCompanyNameException
     */
    public function testSuccess3()
    {
        $authKey = new CompanyName(' test123 ');

        self::assertSame('test123', (string)$authKey);
    }

    /**
     * @throws InvalidCompanyNameException
     */
    public function testInvalidMaxLength()
    {
        $this->expectException(InvalidCompanyNameException::class);
        $this->expectExceptionMessage('The length of company name should not exceed 30 characters.');

        new CompanyName(str_repeat('a', 35));
    }
}
