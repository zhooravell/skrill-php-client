<?php

declare(strict_types=1);

namespace Skrill\Tests\ValueObject;

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
    public function testSuccess($value)
    {
        $currency = new Language($value);

        self::assertEquals($value, (string) $currency);
    }

    /**
     * @return array
     */
    public function successDataProvider()
    {
        $result = [];

        foreach (getSkillSupportsLanguages() as $lang => $title) {
            $result[] = [$lang];
        }

        return $result;
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
