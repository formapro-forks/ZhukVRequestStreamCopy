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

use RequestStream\Request\Web\CookiesBag;
use RequestStream\Request\Web\Cookie;

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

        $this->assertEquals('name', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());

        $this->assertNull($cookie->getExpires());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertFalse($cookie->getSecure());
        $this->assertTrue($cookie->getHttpOnly());

        $expires = new \DateTime('now', new \DateTimeZone('UTC'));
        $cookie = new Cookie('name', 'value', $expires, '/', 'example.com', TRUE, FALSE);

        $this->assertEquals($expires, $cookie->getExpires());
        $this->assertEquals('example.com', $cookie->getDomain());
        $this->assertTrue($cookie->getSecure());
        $this->assertFalse($cookie->getHttpOnly());
    }

    /**
     * Create cookie from string test
     */
    public function testParseCookieFromString()
    {
        $cookie = Cookie::parseFromString('name=value');

        $this->assertEquals('name', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());
        $this->assertNull($cookie->getExpires());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertNull($cookie->getDomain());
        $this->assertFalse($cookie->getSecure());
        $this->assertTrue($cookie->getHttpOnly());

        $this->assertEquals((string) $cookie, 'name=value');

        $cookie = Cookie::parseFromString('name2=value2; expires=Fri, 31 Dec 2010 23:59:59 GMT; path=/path; domain=example.com');

        $this->assertEquals('name2', $cookie->getName());
        $this->assertEquals('value2', $cookie->getValue());
        //$expires = new \DateTime('31 Dec 2010 23:59:59 GMT');
        //$this->assertEquals($cookie->getExpires()->format('U'), $expires->format('U'));
        //$this->assertEquals($cookie->getPath(), '/path');
        //$this->assertEquals($cookie->getDomain(), 'example.com');

        $this->assertEquals('name2=value2', (string) $cookie);
    }

    /**
     * Cookies bag test
     */
    public function testCookiesBag()
    {
        $bag = new CookiesBag;

        $bag['c1'] = 'v1';

        $this->assertEquals(1, count($bag));
        $this->assertInstanceOf('RequestStream\Request\Web\Cookie', $bag['c1']);

        $bag['c2'] = 'v2';
        $this->assertEquals('v2', $bag['c2']->getValue());
        $this->assertEquals('c2', $bag['c2']->getName());

        $cookie = new Cookie('c3', 'v3');
        $bag[] = $cookie;
        $this->assertEquals('c3', $bag['c3']->getName());
        $this->assertEquals('v3', $bag['c3']->getValue());

        $this->assertEquals('c1=v1;c2=v2;c3=v3', str_replace(' ', '', (string) $bag));
    }

    /**
     * Cookie filter test
     */
    public function testCookieFilter()
    {
        // TODO: Create cookie filter tests
    }
}