<?php

declare(strict_types=1);

namespace Skrill\Tests\Response;

use ReflectionClass;
use ReflectionException;
use Skrill\Response\Response;
use PHPUnit\Framework\TestCase;
use Skrill\Exception\ResponseDataException;

/**
 * Class ResponseTest.
 */
class ResponseTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testIsFinal()
    {
        $method = new ReflectionClass(Response::class);

        $this->assertTrue($method->isFinal());
    }

    /**
     * @dataProvider dotNotationDataProvider
     *
     * @param string     $key
     * @param mixed      $expected
     * @param array      $data
     * @param mixed|null $default
     */
    public function testDotNotation($key, $expected, array $data, $default = null)
    {
        $this->assertEquals($expected, (new Response($data))->get($key, $default));
    }

    /**
     * @return array
     */
    public function dotNotationDataProvider()
    {
        return [
            'level 1' => [
                'response_text',
                'Site successfully created',
                [
                    'response_text' => 'Site successfully created',
                    'site_id' => 13,
                ],
                null,
            ],
            'level 2' => [
                'periods.current',
                '2012-06-11 2012-06-25',
                [
                    'response_text' => "Information about client's periods.",
                    'periods' => [
                        'current' => '2012-06-11 2012-06-25',
                    ],
                ],
                null,
            ],
            'level 3' => [
                'invoices.cc.USD',
                [
                    'currency' => 'USD',
                    'credits' => 174.8,
                ],
                [
                    'invoices' => [
                        'cc' => [
                            'USD' => [
                                'currency' => 'USD',
                                'credits' => 174.8,
                            ],
                        ],
                    ],
                ],
                null,
            ],
            'level 4' => [
                'invoices.cc.USD.credits',
                174.8,
                [
                    'invoices' => [
                        'cc' => [
                            'USD' => [
                                'currency' => 'USD',
                                'credits' => 174.8,
                            ],
                        ],
                    ],
                ],
                null,
            ],
            'default' => [
                'invoices.cc.USD.credits',
                '---DEFAULT---',
                [
                    'invoices' => [
                        'cc' => [
                            'EUR' => [
                                'currency' => 'USD',
                                'credits' => 174.8,
                            ],
                        ],
                    ],
                ],
                '---DEFAULT---',
            ],
            'empty' => [
                'invoices.cc.USD.credits',
                null,
                [],
                null,
            ],
            'empty key' => [
                '',
                [
                    'response_text' => 'Site successfully created',
                    'site_id' => 13,
                ],
                [
                    'response_text' => 'Site successfully created',
                    'site_id' => 13,
                ],
                null,
            ],
            'key not exists' => [
                'test',
                '---DEFAULT---',
                [
                    'response_text' => 'Site successfully created',
                    'site_id' => 13,
                ],
                '---DEFAULT---',
            ],
        ];
    }

    public function testSetValueException()
    {
        $this->expectException(ResponseDataException::class);

        $response = new Response([]);
        $response->userId = 1;
    }

    public function testIssetProperty()
    {
        $response = new Response(['userId' => 111]);

        $this->assertTrue(isset($response->userId));
        $this->assertFalse(isset($response->address));
    }

    public function testGetPropertyValue()
    {
        $userId = 111;
        $response = new Response(['userId' => $userId]);

        $this->assertEquals($userId, $response->userId);
    }

    public function testGetNotExistsPropertyValue()
    {
        $userId = 111;
        $response = new Response(['userId' => $userId]);

        $this->assertNull($response->address);
    }

    public function testGetValuesMixedApproach()
    {
        $userId = 111;
        $street = '4094 Kansas St San Diego, CA 92104';
        $response = new Response([
            'userId' => $userId,
            'address' => [
                'street' => $street,
            ],
        ]);

        $this->assertNull($response->get('address.street_2'));
        $this->assertEquals($street, $response->get('address.street'));
        $this->assertEquals($userId, $response->userId);
        $this->assertEquals($userId, $response->get('userId'));
        $this->assertEquals(['street' => $street], $response->address);
    }
}
