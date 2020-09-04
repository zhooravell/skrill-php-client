<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Generator;
use Skrill\ValueObject\SecretWord;
use Skrill\Exception\InvalidSecretWordException;

/**
 * Class SecretWordTest.
 */
class SecretWordTest extends StringValueObjectTestCase
{
    /**
     * @throws InvalidSecretWordException
     */
    public function testSuccess()
    {
        $value = 'test123';

        self::assertSame($value, (string)(new SecretWord($value)));
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testSuccess2()
    {
        self::assertSame('test123', (string)(new SecretWord(' test123 ')));
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testInvalidMaxLength()
    {
        $this->expectException(InvalidSecretWordException::class);
        $this->expectExceptionMessage('The length of Skrill secret word should not exceed 10 characters.');

        new SecretWord(str_repeat('a', 35));
    }

    /**
     * @dataProvider emptyStringDataProvider
     *
     * @param string $value
     *
     * @throws InvalidSecretWordException
     */
    public function testEmptyValue(string $value)
    {
        $this->expectException(InvalidSecretWordException::class);
        $this->expectExceptionMessage('Skrill secret word should not be blank.');

        new SecretWord($value);
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
        $this->expectException(InvalidSecretWordException::class);
        $this->expectExceptionMessage('Special characters are not permitted in Skrill secret word.');

        new SecretWord($value);
    }

    /**
     * @return Generator
     */
    public function specialCharactersValueProvider(): Generator
    {
        yield ['test@'];
        yield ['$test'];
        yield ['t%st'];
    }
}
