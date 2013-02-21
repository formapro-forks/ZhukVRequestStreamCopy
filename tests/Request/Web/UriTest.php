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

use RequestStream\Request\Web\Uri;

/**
 * URI tests
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default test uri
     */
    public function testBase()
    {
        $uri = new Uri('google.com');

        $this->assertEquals($uri->getHost(), 'google.com');
        $this->assertNull($uri->getPort());
        $this->assertEquals($uri->getPath(), '/');
        $this->assertFalse($uri->getSecure());
        $this->assertEquals(count($uri->getQuery()), 0);
        $this->assertNull($uri->getFragment());

        $this->assertEquals((string) $uri, 'http://google.com/');
    }

    /**
     * Parse uri from string
     */
    public function testParseUriFromString()
    {
        $uri = Uri::parseFromString('google.com');

        $this->assertEquals($uri->getHost(), 'google.com');
        $this->assertNull($uri->getPort());
        $this->assertEquals($uri->getPath(), '/');
        $this->assertFalse($uri->getSecure());
        $this->assertEquals(count($uri->getQuery()), 0);
        $this->assertNull($uri->getFragment());

        $uri = Uri::parseFromString('https://user:password@google.com/search?q=Query');

        $this->assertEquals($uri->getHost(), 'google.com');
        $this->assertNull($uri->getPort());
        $this->assertEquals($uri->getPath(), '/search');
        $this->assertTrue($uri->getSecure());
        $this->assertEquals(count($uri->getQuery()), 1);
        $this->assertEquals($uri->getQuery(), array('q=Query'));
        $this->assertNull($uri->getFragment());
        $this->assertEquals($uri->getUserLogin(), array('user' => 'user', 'password' => 'password'));

        $this->assertEquals((string) $uri, 'https://user:password@google.com/search?q=Query');
    }
}