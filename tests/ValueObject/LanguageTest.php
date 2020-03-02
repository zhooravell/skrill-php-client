<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

use Generator;
use PHPUnit\Framework\TestCase;
use Skrill\ValueObject\Language;
use Skrill\Exception\InvalidLangException;

/**
 * Class LanguageTest.
 */
class LanguageTest extends TestCase
{
    /**
     * @dataProvider successDataProvider
     *
     * @param string $value
     *
     * @throws InvalidLangException
     */
    public function testSuccess(string $value)
    {
        self::assertEquals($value, new Language($value));
    }

    /**
     * @throws InvalidLangException
     */
    public function testSuccess2()
    {
        self::assertEquals('FR', new Language('FR '));
    }

    /**
     * @return Generator
     */
    public function successDataProvider(): Generator
    {
        foreach (getSkillSupportsLanguages() as $lang => $title) {
            yield [$lang];
        }
    }

    /**
     * @throws InvalidLangException
     */
    public function testInvalidValue()
    {
        self::expectException(InvalidLangException::class);
        self::expectExceptionMessage('Not accepted language by Skrill.');

        new Language('test');
    }
}
