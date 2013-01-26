<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

use RequestStream\Request\Web\CookiesBag,
    RequestStream\Request\Web\Cookie,
    RequestStream\Request\ParametersBagInterface;

/**
 * Cookie, CookiesBag, CookieFilter tests
 */
class CookiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base test cookie
     */
    public function testCookie()
    {
        $cookie = new Cookie('name', 'value');

        $this->assertEquals($cookie->getName(), 'name');
        $this->assertEquals($cookie->getValue(), 'value');

        $this->assertNull($cookie->getExpires());
        $this->assertEquals($cookie->getPath(), '/');
        $this->assertNull($cookie->getDomain());
        $this->assertFalse($cookie->getSecure());
        $this->assertTrue($cookie->getHttpOnly());

        $expires = new \DateTime('now', new \DateTimeZone('UTC'));
        $cookie = new Cookie('name', 'value', $expires, '/', 'example.com', TRUE, FALSE);

        $this->assertEquals($cookie->getExpires(), $expires);
        $this->assertEquals($cookie->getDomain(), 'example.com');
        $this->assertTrue($cookie->getSecure());
        $this->assertFalse($cookie->getHttpOnly());
    }

    /**
     * Create cookie from string test
     */
    public function testParseCookieFromString()
    {
        $cookie = Cookie::parseFromString('name=value');

        $this->assertEquals($cookie->getName(), 'name');
        $this->assertEquals($cookie->getValue(), 'value');
        $this->assertNull($cookie->getExpires());
        $this->assertEquals($cookie->getPath(), '/');
        $this->assertNull($cookie->getDomain());
        $this->assertFalse($cookie->getSecure());
        $this->assertTrue($cookie->getHttpOnly());

        $this->assertEquals((string) $cookie, 'name=value');

        $cookie = Cookie::parseFromString('name2=value2; expires=Fri, 31 Dec 2010 23:59:59 GMT; path=/path; domain=example.com');

        $this->assertEquals($cookie->getName(), 'name2');
        $this->assertEquals($cookie->getValue(), 'value2');
        //$expires = new \DateTime('31 Dec 2010 23:59:59 GMT');
        //$this->assertEquals($cookie->getExpires()->format('U'), $expires->format('U'));
        //$this->assertEquals($cookie->getPath(), '/path');
        //$this->assertEquals($cookie->getDomain(), 'example.com');

        $this->assertEquals((string) $cookie, 'name2=value2');
    }

    /**
     * Cookies bag test
     */
    public function testCookiesBag()
    {
        $bag = new CookiesBag;

        $bag['c1'] = 'v1';

        $this->assertEquals(count($bag), 1);
        $this->assertTrue($bag['c1'] instanceof Cookie);

        $bag['c2'] = 'v2';
        $this->assertEquals($bag['c2']->getValue(), 'v2');
        $this->assertEquals($bag['c2']->getName(), 'c2');

        $cookie = new Cookie('c3', 'v3');
        $bag[] = $cookie;
        $this->assertEquals($bag['c3']->getName(), 'c3');
        $this->assertEquals($bag['c3']->getValue(), 'v3');

        $this->assertEquals(str_replace(' ', '', (string) $bag), 'c1=v1;c2=v2;c3=v3');
    }

    /**
     * Cookie filter test
     */
    public function testCookieFilter()
    {
        // TODO: Create cookie filter tests
    }
}