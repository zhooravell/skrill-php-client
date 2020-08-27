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
        $value = 'sw_wefghjylpg';

        self::assertEquals($value, new SecretWord($value));
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testSuccess2()
    {
        self::assertEquals('sw_wefghjylpg', new SecretWord(' sw_wefghjylpg '));
    }

    /**
     * @throws InvalidSecretWordException
     */
    public function testInvalidMaxLength()
    {
        $this->expectException(InvalidSecretWordException::class);
        $this->expectExceptionMessage(
            sprintf('The length of Skrill Secret Word is too short. It should have %d characters or more.',
            SecretWord::MIN_LENGTH));

        new SecretWord(str_repeat('a', 5));
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
        $this->expectExceptionMessage('Skrill Secret Word must include at least one non-alphabetic character.');

        new SecretWord($value);
    }

    /**
     * @return Generator
     */
    public function specialCharactersValueProvider(): Generator
    {
        yield ['qwertyuio'];
    }
}
