<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Email;
use Skrill\Exception\InvalidEmailException;

/**
 * Class EmailTest.
 */
class EmailTest extends TestCase
{
    /**
     * @throws InvalidEmailException
     */
    public function testSuccess()
    {
        $value = 'test@test.com';
        $authKey = new Email($value);

        self::assertEquals($value, (string) $authKey);
    }

    /**
     * @throws InvalidEmailException
     */
    public function testInvalidValue()
    {
        self::expectException(InvalidEmailException::class);
        self::expectExceptionMessage('Email is not a valid email address.');

        new Email('test');
    }
}
