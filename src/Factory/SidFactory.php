<?php

declare(strict_types=1);

namespace Skrill\Factory;

use DateTimeImmutable;
use Skrill\ValueObject\Sid;
use Psr\Http\Message\ResponseInterface;
use Skrill\Exception\InvalidSidException;
use Skrill\Exception\SkrillResponseException;

/**
 * Class SidFactory.
 */
final class SidFactory
{
    /**
     * @param string $value
     *
     * @return Sid
     *
     * @throws InvalidSidException
     * @throws \Exception
     */
    public static function createFromString(string $value): Sid
    {
        return new Sid($value, (new DateTimeImmutable())->modify('+15 minutes'));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Sid
     *
     * @throws InvalidSidException
     * @throws SkrillResponseException
     */
    public static function createFromXMLResponse(ResponseInterface $response): Sid
    {
        $xml = new \SimpleXMLElement($response->getBody()->getContents());

        if ($xml->xpath('error/error_msg')) {
            throw SkrillResponseException::fromSkillError((strval($xml->error->error_msg)));
        }

        return self::createFromString(strval($xml->sid));
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Sid
     *
     * @throws InvalidSidException
     * @throws SkrillResponseException
     */
    public static function createFromSaleResponse(ResponseInterface $response): Sid
    {
        $content = $response->getBody()->getContents();
        $result = json_decode($content);

        if (JSON_ERROR_NONE == json_last_error()) {
            throw SkrillResponseException::fromSkillError($result->message);
        }

        return self::createFromString($content);
    }
}
