<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Password;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class PasswordTest.
 */
class PasswordTest extends TestCase
{
    /**
     * @throws InvalidPasswordException
     */
    public function testSuccess()
    {
        $value = 'a1234567';
        $secretWord = new Password($value);

        self::assertEquals($value, (string) $secretWord);
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testInvalidMinLength()
    {
        self::expectException(InvalidPasswordException::class);
        self::expectExceptionMessage('Skrill API/MQI password is too short. It should have 8 characters or more.');

        new Password('a123');
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testEmpty()
    {
        self::expectException(InvalidPasswordException::class);
        self::expectExceptionMessage('Skrill API/MQI password is too short. It should have 8 characters or more.');

        new Password(' ');
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testMissingLetters()
    {
        self::expectException(InvalidPasswordException::class);
        self::expectExceptionMessage('Skrill API/MQI password must include at least one letter.');

        new Password('12345678');
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testMissingNumbers()
    {
        self::expectException(InvalidPasswordException::class);
        self::expectExceptionMessage('Skrill API/MQI password must include at least one number.');

        new Password('qwertyui');
    }
}
