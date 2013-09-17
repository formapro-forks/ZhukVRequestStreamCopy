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

use RequestStream\Request\Web\Result;
use RequestStream\Request\Web\HeadersBag;
use RequestStream\Request\Web\CookiesBag;
use RequestStream\Request\Web\PostDataBag;
use RequestStream\Request\Web\RequestInterface;
use RequestStream\Request\Web\DefaultRequest;
use RequestStream\Request\Web\PostRequest;
use RequestStream\Request\Web\Uri;
use RequestStream\Request\Web\Proxy;

/**
 * Request types test
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default request test
     */
    public function testDefaultRequest()
    {
        $request = new DefaultRequest();

        $this->assertTrue($request instanceof RequestInterface);
        $this->assertTrue($request->getHeaders() instanceof HeadersBag);
        $this->assertTrue($request->getCookies() instanceof CookiesBag);

        $this->assertEquals($request->getMethod(), 'GET');
        $this->assertEquals($request->getHttpVersion(), '1.0');
        $this->assertNull($request->getUri());

        $request->setMethod('HEAD');
        $this->assertEquals($request->getMethod(), 'HEAD');

        $request->setHttpVersion('1.1');
        $this->assertEquals($request->getHttpVersion(), '1.1');

        try {
            $request->setMethod('FOO');
            $this->fail('Not control HTTP method.');
        }
        catch (\InvalidArgumentException $e) {
        }

        $uri = new Uri('http://example.com/');
        $request->setUri($uri);
        $this->assertEquals($request->getUri(), $uri);

        $proxy = new Proxy('http://example.com');
        $request->setProxy($proxy);
        $this->assertEquals($request->getProxy(), $proxy);

        // Prepare test
        $request->prepare();
        $this->assertFalse($request->getHeaders()->offsetExists('Cookie'));
        $this->assertTrue($request->getHeaders()->offsetExists('Accept'));

        $request->getCookies()->offsetSet('name', 'value');
        $request->prepare();
        $this->assertEquals($request->getCookies()->offsetGet('name')->getValue(), 'value');

        $request = new DefaultRequest;
        $request->setUri(Uri::parseFromString('http://example.com'));

        $headers = "GET / HTTP/1.0\r\n" .
            "Host: example.com\r\n" .
            "Accept: */*\r\n\r\n";

        $this->assertEquals((string) $request, $headers);
    }

    /**
     * Test post request
     */
    public function testPostRequest()
    {
        $request = new PostRequest;

        $this->assertTrue($request instanceof DefaultRequest);
        $this->assertEquals($request->getMethod(), 'POST');
        $this->assertTrue($request->getPostData() instanceof PostDataBag);

        try {
            $request->setMethod('GET');
            $this->fail('Not control set method in PostRequest');
        } catch (\BadMethodCallException $e) {
        }

        // Prepare test
        $request->prepare();

        $this->assertTrue($request->getHeaders()->offsetExists('Accept'));
        $this->assertTrue($request->getHeaders()->offsetExists('Content-Type'));
        $this->assertTrue($request->getHeaders()->offsetExists('Content-Length'));
    }
}