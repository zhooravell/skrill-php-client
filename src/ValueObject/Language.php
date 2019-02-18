<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidLangException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for language.
 *
 * @see https://en.wikipedia.org/wiki/ISO_639-1
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class Language
{
    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidLangException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (!array_key_exists($value, getSkillSupportsLanguages())) {
            throw InvalidLangException::invalidValue();
        }

        $this->value = $value;
    }
}
