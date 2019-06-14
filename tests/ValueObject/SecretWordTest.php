<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\SecretWord;
use Skrill\Exception\InvalidSecretWordException;

/**
 * Class SecretWordTest.
 */
class SecretWordTest extends TestCase
{
    /**
     * @throws InvalidSecretWordException
     */
    public function testSuccess()
    {
        $value = 'test123';
        $secretWord = new SecretWord($value);

        self::assertEquals($value, (string) $secretWord);
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testInvalidMaxLength()
    {
        self::expectException(InvalidSecretWordException::class);
        self::expectExceptionMessage('The length of Skrill secret word should not exceed 10 characters.');

        new SecretWord(str_repeat('a', 35));
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testEmptyValue()
    {
        self::expectException(InvalidSecretWordException::class);
        self::expectExceptionMessage('Skrill secret word should not be blank.');

        new SecretWord(' ');
    }

    /**
     * @dataProvider specialCharactersValueProvider
     *
     * @param string $value
     *
     * @throws InvalidSecretWordException
     */
    public function testSpecialCharacters($value)
    {
        self::expectException(InvalidSecretWordException::class);
        self::expectExceptionMessage('Special characters are not permitted in Skrill secret word.');

        new SecretWord($value);
    }

    /**
     * @return array
     */
    public function specialCharactersValueProvider(): array
    {
        return [
            ['test@'],
            ['$test'],
            ['t%st'],
        ];
    }
}
