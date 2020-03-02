<?php

declare(strict_types=1);

namespace Skrill\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class HelpersTest.
 */
class HelpersTest extends TestCase
{
    public function testResourcesFilesExists()
    {
        self::assertTrue(file_exists(__DIR__ . '/../resources/iso-3166-1-alpha-3-countries-skill-supports.php'));
        self::assertTrue(file_exists(__DIR__ . '/../resources/iso-6391-languages-skrill-supports.php'));
    }

    public function testGetSkillSupportsCountries()
    {
        self::assertTrue(function_exists('getSkillSupportsCountries'));

        $countries = getSkillSupportsCountries();

        self::assertTrue(is_array($countries));
        self::assertCount(238, $countries);

        foreach ($countries as $country => $title) {
            self::assertEquals(3, mb_strlen($country));
        }

        self::assertArrayNotHasKey('CUB', $countries);
        self::assertArrayNotHasKey('AFG', $countries);
        self::assertArrayNotHasKey('ERI', $countries);
        self::assertArrayNotHasKey('IRN', $countries);
        self::assertArrayNotHasKey('IRQ', $countries);
        self::assertArrayNotHasKey('JPN', $countries);
        self::assertArrayNotHasKey('KGZ', $countries);
        self::assertArrayNotHasKey('LBY', $countries);
        self::assertArrayNotHasKey('SDN', $countries);
        self::assertArrayNotHasKey('SYR', $countries);
        self::assertArrayNotHasKey('PRK', $countries);
    }

    public function testGetSkillSupportsLanguages()
    {
        self::assertTrue(function_exists('getSkillSupportsLanguages'));

        $languages = getSkillSupportsLanguages();

        self::assertTrue(is_array($languages));
        self::assertCount(18, $languages);

        foreach ($languages as $language => $title) {
            self::assertEquals(2, mb_strlen($language));
        }
    }
}
