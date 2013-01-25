<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\HeadersBag,
    RequestStream\Request\Web\CookiesBag,
    RequestStream\Request\ParametersBagInterface,
    RequestStream\Request\Web\Uri;

/**
 * Headers bag test
 */
class HeadersBagRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Headers bag test
     */
    public function testHeadersBag()
    {
        $bag = new HeadersBag;
        $this->assertTrue($bag instanceof ParametersBagInterface);

        $bag['k1'] = 'v1';

        $this->assertEquals(count($bag), 1);
        $this->assertEquals($bag['k1'], 'v1');
        unset ($bag['k1']);
        $this->assertEquals(count($bag), 0);

        $bag['Referer'] = 'http://google.com';
        $this->assertTrue($bag['Referer'] instanceof Uri);
        $this->assertEquals((string) $bag['Referer'], 'http://google.com/');

        try {
            $bag['cookie'] = 'cookie1';
            $this->fail('Not control cookie bag in headers.');
        }
        catch (\InvalidArgumentException $e)
        {
        }

        try {
            $bag['tmp'] = array();
            $this->fail('Not control header data type.');
        }
        catch (\InvalidArgumentException $e)
        {
        }

        $cookiesBag = new CookiesBag;
        $bag['cookie'] = $cookiesBag;

        $this->assertEquals($bag['Cookie'], $cookiesBag);

        $bag = new HeadersBag;
        $bag['k1'] = 'v1';
        $bag['k2'] = 'v2';
        $this->assertEquals((string) $bag, "k1: v1\nk2: v2");
    }
}