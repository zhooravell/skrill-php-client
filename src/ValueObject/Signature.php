<?php

declare(strict_types=1);

namespace Skrill\ValueObject;

use Skrill\Exception\InvalidSignatureException;
use Skrill\ValueObject\Traits\ValueToStringTrait;

/**
 * Value object for Skrill signature (md5sig or sha2sig).
 *
 * @see https://www.skrill.com/fileadmin/content/pdf/Skrill_Quick_Checkout_Guide.pdf
 */
final class Signature
{
    use ValueToStringTrait;

    /**
     * @param string $value
     *
     * @throws InvalidSignatureException
     */
    public function __construct(string $value)
    {
        $value = trim($value);

        if (empty($value)) {
            throw InvalidSignatureException::emptySignature();
        }

        if (preg_match('/[a-z]/', $value)) {
            throw InvalidSignatureException::lowercase();
        }

        $this->value = $value;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function equalToString($string): bool
    {
        return $string == strval($this->value);
    }
}
