<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\Cookie;

/**
 * Cookie tests
 */
class CookiesRequestTest extends \PHPUnit_Framework_TestCase
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
        $expires = new \DateTime('31 Dec 2010 23:59:59 GMT');
        $this->assertEquals($cookie->getExpires()->format('U'), $expires->format('U'));
        $this->assertEquals($cookie->getPath(), '/path');
        $this->assertEquals($cookie->getDomain(), 'example.com');

        $this->assertEquals((string) $cookie, 'name2=value2');
    }
}