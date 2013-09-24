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

use RequestStream\Request\Web\ContentDataCompiler\CompilerFactory;

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
     * @var mixed
     */
    protected $contentData;

    /**
     * @var bool
     */
    protected $autoContentType = true;

    /**
     * @var string
     */
    protected $contentDataCompiler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->headers = new HeadersBag;
        $this->cookies = new CookiesBag;
    }

    /**
     * Set headers
     *
     * @param array|\Traversable|HeadersBag $headers
     * @throws \InvalidArgumentException
     */
    public function setHeaders($headers)
    {
        if (!$headers instanceof HeadersBag && !is_array($headers) && !$headers instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Headers must be array or HeadersBag instance, "%s" given.',
                is_object($headers) ? get_class($headers) : gettype($headers)
            ));
        }

        if ($headers instanceof HeadersBag) {
            $this->headers = $headers;
        } else {
            $this->headers = new HeadersBag($headers);
        }

        return $this;
    }

    /**
     * Get headers
     *
     * @return HeadersBag
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set cookies
     *
     * @param array|\Traversable|CookiesBag $cookies
     * @throws \InvalidArgumentException
     */
    public function setCookies($cookies)
    {
        if (!$cookies instanceof CookiesBag && !is_array($cookies) && !$cookies instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Cookies must be array or CookiesBag instance, "%s" given.',
                is_object($cookies) ? get_class($cookies) : gettype($cookies)
            ));
        }

        if ($cookies instanceof CookiesBag) {
            $this->cookies = $cookies;
        } else {
            $this->cookies = new CookiesBag($cookies);
        }

        return $this;
    }

    /**
     * Get cookies bag
     *
     * @return CookiesBag
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Set request uri
     *
     * @param string|Uri $$uri
     */
    public function setUri($uri)
    {
        if ($uri instanceof Uri) {
            $this->uri = $uri;
        } else {
            $this->uri = Uri::parseFromString($uri);
        }

        return $this;
    }

    /**
     * Get uri
     *
     * @return Uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Set proxy
     *
     * @param Proxy $proxy
     */
    public function setProxy(Proxy $proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Get proxy
     *
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Set content data to request
     *
     * @param mixed $contentData
     * @param null|string $compiler
     */
    public function setContentData($contentData, $compiler = null)
    {
        $this->contentData = $contentData;

        if (null !== $compiler) {
            $this->contentDataCompiler = $compiler;
        }

        return $this;
    }

    /**
     * Get content data
     *
     * @return mixed
     */
    public function getContentData()
    {
        return $this->contentData;
    }

    /**
     * Set auto control content type, if content data already exists
     *
     * @param bool $auto
     */
    public function setAutoContentType($auto = true)
    {
        $this->autoContentType = (bool) $auto;

        return $this;
    }

    /**
     * Get status of auto control content type
     *
     * @return bool
     */
    public function getAutoContentType()
    {
        return $this->autoContentType;
    }

    /**
     * Set content data compiler for compile content data
     *
     * @param string $compiler
     */
    public function setContentDataCompiler($compiler)
    {
        $this->contentDataCompiler = $compiler;

        return $this;
    }

    /**
     * Get content data compiler
     *
     * @return string
     */
    public function getContentDataCompiler()
    {
        return $this->contentDataCompiler;
    }

    /**
     * Set request method
     *
     * @param string $method
     * @throws \InvalidArgumentException
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
     * Get request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set http version of request
     *
     * @param string $httpVersion
     */
    public function setHttpVersion($httpVersion)
    {
        $this->httpVersion = $httpVersion;

        return $this;
    }

    /**
     * Get http version
     *
     * @return string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * Prepare request
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

        // Check content data type
        if ($this->contentData && $this->autoContentType) {
            if (!isset($this->headers['Content-Type'])) {
                if ($this->contentData instanceof \DOMDocument) {
                    $this->headers['Content-Type'] = 'application/xml';
                } else if ($this->contentData instanceof \JsonSerializable) {
                    $this->headers['Content-Type'] = 'application/json';
                } else if (is_string($this->contentData)) {
                    $this->headers['Content-Type'] = 'text/plain';
                }
            }
        }

        if ($this->contentData && !isset($this->headers['Content-Length'])) {
            // Content data already exists. Add Content-Length header
            $contentData = CompilerFactory::compile($this->contentDataCompiler, $this->contentData);
            $this->headers['Content-Length'] = mb_strlen($contentData);
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

        if ($this->contentData) {
            $contentData = CompilerFactory::compile($this->contentDataCompiler, $this->contentData);
        } else {
            $contentData = null;
        }

        return $this->method . ' ' . ($this->uri->getPath() . ($this->uri->getQuery() ? '?' . implode('&', $this->uri->getQuery()) : '')) . ' HTTP/' . $this->httpVersion .  "\r\n" .
            'Host: ' . $this->uri->getHost() . "\r\n".
            ((string) $this->headers) .
            "\r\n\r\n" .
            ($contentData ? trim($contentData) . "\r\n\r\n" : '');
    }
}