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

        self::assertEquals($value, (string) $authKey);
    }

    /**
     * @throws InvalidCompanyNameException
     */
    public function testSuccess2()
    {
        $value = str_repeat('a', 30);
        $authKey = new CompanyName($value);

        self::assertEquals($value, (string) $authKey);
    }

    /**
     * @throws InvalidCompanyNameException
     */
    public function testInvalidMaxLength()
    {
        self::expectException(InvalidCompanyNameException::class);
        self::expectExceptionMessage('The length of company name should not exceed 30 characters.');

        new CompanyName(str_repeat('a', 35));
    }
}
