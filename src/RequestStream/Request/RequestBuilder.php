<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request;

use RequestStream\Request\Web\CookiesBag;
use RequestStream\Request\Web\DefaultRequest;
use RequestStream\Request\Web\HeadersBag;
use RequestStream\Request\Web\PostDataBag;
use RequestStream\Request\Web\PostRequest;
use RequestStream\Request\Web\Proxy;

/**
 * Request builder
 */
class RequestBuilder
{
    /**
     * @var CookiesBag
     */
    private $cookiesBag;

    /**
     * @var HeadersBag
     */
    private $headersBag;

    /**
     * @var PostDataBag
     */
    private $postDataBag;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var string
     */
    private $proxy;

    /**
     * @var mixed
     */
    private $contentData;

    /**
     * @var bool
     */
    private $autoContentType = true;

    /**
     * @var string
     */
    private $contentDataCompiler;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->cookiesBag = new CookiesBag();
        $this->headersBag = new HeadersBag();
        $this->postDataBag = new PostDataBag();
    }

    /**
     * Create a new instance
     *
     * @return RequestBuilder
     */
    public static function newInstance()
    {
        return new static();
    }

    /**
     * Get cookies bag
     *
     * @return CookiesBag
     */
    public function getCookiesBag()
    {
        return $this->cookiesBag;
    }

    /**
     * Get headers bag
     *
     * @return HeadersBag
     */
    public function getHeadersBag()
    {
        return $this->headersBag;
    }

    /**
     * Add cookie
     *
     * @param string $name
     * @param string $value
     * @return RequestBuilder
     */
    public function addCookie($name, $value = null)
    {
        $this->cookiesBag->add($name, $value);

        return $this;
    }

    /**
     * Add header
     *
     * @param string $name
     * @param string $value
     * @return RequestBuilder
     */
    public function addHeader($name, $value = null)
    {
        $this->headersBag->add($name, $value);

        return $this;
    }

    /**
     * Add post data
     *
     * @param string $name
     * @param mixed $value
     * @return RequestBuilder
     */
    public function addPostData($name, $value = null)
    {
        $this->postDataBag->add($name, $value);

        if (!$this->method) {
            $this->method = 'POST';
        }

        return $this;
    }

    /**
     * Set post data
     *
     * @param array|\Traversable $data
     * @throws \InvalidArgumentException
     * @return RequestBuilder
     */
    public function setPostData($data)
    {
        if (!is_array($data) && !$data instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Data must be a array or \Traversable instance, "%s" given.',
                is_object($data) ? get_class($data) : gettype($data)
            ));
        }

        $this->postDataBag->removeAll();

        foreach ($data as $key => $value) {
            $this->addPostData($key, $value);
        }

        return $this;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return RequestBuilder
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set request uri
     *
     * @param string $uri
     * @return RequestBuilder
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Set content data
     *
     * @param mixed $contentData
     * @param string $compiler
     * @return RequestBuilder
     */
    public function setContentData($contentData, $compiler = null)
    {
        $this->contentData = $contentData;
        $this->contentDataCompiler = $compiler;

        return $this;
    }

    /**
     * Set auto content type
     *
     * @param bool $status
     * @return RequestBuilder
     */
    public function setAutoContentType($status = true)
    {
        $this->autoContentType = (bool) $status;

        return $this;
    }

    /**
     * Set content data compiler
     *
     * @param string $compiler
     * @return RequestBuilder
     */
    public function setContentDataCompiler($compiler)
    {
        $this->contentDataCompiler = (bool) $compiler;

        return $this;
    }

    /**
     * Set proxy
     *
     * @param string|Web\Proxy $proxy
     * @return RequestBuilder
     */
    public function setProxy($proxy)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Get request
     *
     * @throws \InvalidArgumentException
     * @return Web\RequestInterface
     */
    public function getRequest()
    {
        if (!$this->method) {
            // Set default http method: GET
            $this->method = 'GET';
        }

        switch (mb_strtoupper($this->method)) {
            case 'POST':
                $request = $this->createPostRequest();
                break;

            default:
                $request = $this->createDefaultRequest();
                break;
        }

        $request
            ->setUri($this->uri)
            ->setHeaders($this->headersBag)
            ->setCookies($this->cookiesBag);

        // Check proxy
        if ($this->proxy) {
            if ($this->proxy instanceof Proxy) {
                $request->setProxy($this->proxy);
            } else if (is_string($this->proxy)) {
                $request->setProxy(Proxy::parseFromString($this->proxy));
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Proxy must be a string or Proxy object, "%s" given.',
                    is_object($this->proxy) ? get_class($this->proxy) : gettype($this->proxy)
                ));
            }
        }

        return $request;
    }

    /**
     * Create default request
     *
     * @return DefaultRequest
     */
    private function createDefaultRequest()
    {
        $request = new DefaultRequest();
        $request->setMethod($this->method);
        $request->setContentData($this->contentData, $this->contentDataCompiler);
        $request->setAutoContentType($this->autoContentType);

        return $request;
    }

    /**
     * Create post request
     *
     * @return PostRequest
     */
    private function createPostRequest()
    {
        if ($this->contentData) {
            // POST request are used content data with boundary
            throw new \InvalidArgumentException('Can\'t use content data in POST request. Please remove content data from builder.');
        }

        $request = new PostRequest();
        $request->setPostData($this->postDataBag);

        return $request;
    }
}