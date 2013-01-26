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

/**
 * Default request
 */
class DefaultRequest implements RequestInterface
{
    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $httpVersion = '1.0';

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var HeadersBag
     */
    protected $headers;

    /**
     * @var CookiesBag
     */
    protected $cookies;

    /**
     * @var Proxy
     */
    protected $proxy;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->headers = new HeadersBag;
        $this->cookies = new CookiesBag;
    }

    /**
     * @{inerhitDoc}
     */
    public function setHeaders(HeadersBag $headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @{inerhitDoc}
     */
    public function setCookies(CookiesBag $cookies)
    {
        $this->cookies = $cookies;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @{inerhitDoc}
     */
    public function setUri(Uri $uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @{inerhitDoc}
     */
    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @{inerhitDoc}
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        $allowedMethods = array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT');
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf(
                'Undefined method "%s". Allowed methods: "%s"',
                $method,
                implode('", "', $allowedMethods)
            ));
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @{inerhitDoc}
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * @{inerhitDoc}
     */
    public function prepare()
    {
        // Added Accept header if not exists
        if (!$this->headers->has('Accept')) {
            $this->headers['Accept'] = '*/*';
        }

        // Add cookies
        if (count($this->cookies)) {
            $this->headers['Cookie'] = $this->cookies;
        }
    }

    /**
     * __toString
     */
    public function __toString()
    {
        if (!$this->uri) {
            throw new \RuntimeException('Undefined target request URI.');
        }
        
        $this->prepare();

        return $this->method . ' ' . ($this->uri->getPath()) . ' HTTP/' . $this->httpVersion .  "\r\n" .
            'Host: ' . $this->uri->getHost() . "\r\n".
            ((string) $this->headers) .
            "\r\n\r\n";
    }
}