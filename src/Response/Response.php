<?php

declare(strict_types=1);

namespace Skrill\Response;

use Skrill\Exception\ResponseDataException;

/**
 * Class to represent response data.
 * Provides two approaches:
 * * "dot" notation - $res->get('sender.iban');
 * * property access - $res->currency_id;.
 */
final class Response
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Get an item using "dot" notation.
     * Examples: $res->get("user.id");.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (0 === count($this->data)) {
            return $default;
        }

        if (null === $key || empty($key)) {
            return $this->data;
        }

        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (false === strpos($key, '.')) {
            return $default;
        }

        $array = $this->data;

        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return null;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @throws ResponseDataException
     */
    public function __set($name, $value)
    {
        throw ResponseDataException::reedOnlyMode();
    }
}
