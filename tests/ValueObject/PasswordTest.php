<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Skrill\ValueObject\Password;
use Skrill\Exception\InvalidPasswordException;

/**
 * Class PasswordTest.
 */
class PasswordTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidPasswordException
     */
    public function testSuccess()
    {
        $value = 'a1234567';

        self::assertSame($value, (string)(new Password($value)));
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testSuccess2()
    {
        self::assertSame('a1234567', (string)(new Password(' a1234567 ')));
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testInvalidMinLength()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage('Skrill API/MQI password is too short. It should have 8 characters or more.');

        new Password('a123');
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidPasswordException
     */
    public function testEmpty(string $value)
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage('Skrill API/MQI password is too short. It should have 8 characters or more.');

        new Password($value);
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testMissingLetters()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage('Skrill API/MQI password must include at least one letter.');

        new Password('12345678');
    }

    /**
     * @throws InvalidPasswordException
     */
    public function testMissingNumbers()
    {
        $this->expectException(InvalidPasswordException::class);
        $this->expectExceptionMessage('Skrill API/MQI password must include at least one number.');

        new Password('qwertyui');
    }
}
