<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\Result,
    RequestStream\Request\Web\ResultInterface,
    RequestStream\Request\Web\HeadersBag,
    RequestStream\Request\Web\CookiesBag,
    RequestStream\Request\Web\Cookie;

/**
 * Result tests
 */
class RequestWebResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default test
     */
    public function testBase()
    {
        // Default constructor
        $result = new Result;
        $this->assertTrue($result instanceof ResultInterface);
        $this->assertEquals($result->getCode(), 200);
        $this->assertNull($result->getData());
        $this->assertNull($result->getProtocol());
        $this->assertTrue($result->getHeaders() instanceof HeadersBag);
        $this->assertTrue($result->getCookies() instanceof CookiesBag);
        $this->assertNull($result->getRequestTime());

        $headersBag = new HeadersBag(array('Location' => 'http://google.com'));
        $cookiesBag = new CookiesBag(array('k1' => 'v2'));
        $result = new Result(
            302,
            'Moved....',
            '1.0',
            $headersBag,
            $cookiesBag,
            0.5
        );

        $this->assertEquals($result->getCode(), 302);
        $this->assertEquals($result->getData(), 'Moved....');
        $this->assertEquals($result->getHeaders(), $headersBag);
        $this->assertEquals($result->getCookies(), $cookiesBag);
        $this->assertEquals($result->getRequestTime(), 0.5);
    }

    /**
     * Test parse form string
     */
    public function testParseFromString()
    {
        $content = 'HTTP/1.1 200 OK' . "\n" .
            'Date: Thu, 19 Feb 2009 12:27:04 GMT' . "\n" .
            'ETag: "56d-9989200-1132c580"' . "\n" .
            'Content-Type: text/html; charset="UTF-8"' . "\n" .
            'Set-Cookie: name=value; Expires=Wed, 09-Jun-2021 10:18:14 GMT' . "\r\n\r\n" .

            'Body';

        $result = Result::parseFromContent($content);

        $this->assertTrue($result instanceof ResultInterface);
        $this->assertEquals($result->getProtocol(), 'HTTP/1.1');
        $this->assertEquals($result->getCode(), 200);
        $this->assertEquals($result->headers['ETag'], '56d-9989200-1132c580');
        $this->assertTrue($result->cookies['name'] instanceof Cookie);
        $this->assertEquals($result->cookies['name']->getValue(), 'value');

        $this->assertEquals($result->getData(), 'Body');
    }
}