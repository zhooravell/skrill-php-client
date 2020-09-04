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
        self::assertFileExists(__DIR__ . '/../resources/iso-3166-1-alpha-3-countries-skill-supports.php');
        self::assertFileExists(__DIR__ . '/../resources/iso-6391-languages-skrill-supports.php');
    }

    public function testGetSkillSupportsCountries()
    {
        self::assertTrue(function_exists('getSkillSupportsCountries'));

        $countries = getSkillSupportsCountries();

        self::assertIsArray($countries);
        self::assertCount(238, $countries);

        $countryLists = array_keys($countries);

        foreach ($countryLists as $country) {
            self::assertSame(3, mb_strlen($country));
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

        self::assertIsArray($languages);
        self::assertCount(18, $languages);

        $languageLists = array_keys($languages);

        foreach ($languageLists as $language) {
            self::assertSame(2, mb_strlen($language));
        }
    }
}
