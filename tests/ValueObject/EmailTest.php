<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\Email;
use PHPUnit\Framework\TestCase;
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

        self::assertEquals($value, new Email($value));
    }

    /**
     * @throws InvalidEmailException
     */
    public function testSuccess2()
    {
        self::assertEquals('test@test.com', new Email(' test@test.com '));
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
