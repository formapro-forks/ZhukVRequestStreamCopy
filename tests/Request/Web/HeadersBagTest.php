<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

use RequestStream\Request\Web\HeadersBag;
use RequestStream\Request\Web\CookiesBag;

/**
 * Headers bag test
 */
class HeadersBagTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Headers bag test
     */
    public function testHeadersBag()
    {
        $bag = new HeadersBag;
        $this->assertInstanceOf('RequestStream\Request\ParametersBagInterface', $bag);

        $bag['k1'] = 'v1';

        $this->assertEquals(1, count($bag));
        $this->assertEquals($bag['k1'], 'v1');
        unset ($bag['k1']);
        $this->assertEquals(0, count($bag));

        $bag['Referer'] = 'http://google.com';
        $this->assertInstanceOf('RequestStream\Request\Web\Uri', $bag['Referer']);
        $this->assertEquals('http://google.com/', (string) $bag['Referer']);

        try {
            $bag['cookie'] = 'cookie1';
            $this->fail('Not control cookie bag in headers.');
        } catch (\InvalidArgumentException $e)
        {
        }

        try {
            $bag['tmp'] = array();
            $this->fail('Not control header data type.');
        } catch (\InvalidArgumentException $e)
        {
        }

        $cookiesBag = new CookiesBag;
        $bag['cookie'] = $cookiesBag;

        $this->assertEquals($cookiesBag, $bag['Cookie']);

        $bag = new HeadersBag;
        $bag['k1'] = 'v1';
        $bag['k2'] = 'v2';
        $this->assertEquals("k1: v1\nk2: v2", (string) $bag);
    }
}