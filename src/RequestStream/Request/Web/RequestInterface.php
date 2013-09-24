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

/**
 * Interface for control request types
 */
interface RequestInterface
{
    /**
     * Set headers bag
     *
     * @param array|\Traversable|HeadersBag $headers
     */
    public function setHeaders($headers);

    /**
     * Get headers bag
     *
     * @return HeadersBag
     */
    public function getHeaders();

    /**
     * Set cookies
     *
     * @param array|\Traversable|CookiesBag $cookies
     */
    public function setCookies($cookies);

    /**
     * Get cookies
     *
     * @return CookiesBag
     */
    public function getCookies();

    /**
     * Set uri
     *
     * @param string|Uri $uri
     */
    public function setUri($uri);

    /**
     * Get uri
     *
     * @return Uri
     */
    public function getUri();

    /**
     * Set proxy
     *
     * @param Proxy $proxy
     */
    public function setProxy(Proxy $proxy);

    /**
     * Get proxy
     *
     * @return Proxy
     */
    public function getProxy();

    /**
     * Set method
     *
     * @param string $method
     */
    public function setMethod($method);

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set content data
     *
     * @param mixed $contentData
     * @param null|string|ContentDataCompiler\CompilerInterface $compiler
     */
    public function setContentData($contentData, $compiler = null);

    /**
     * Get content data
     *
     * @return mixed
     */
    public function getContentData();

    /**
     * Set auto generate content type
     *
     * @param bool $status
     */
    public function setAutoContentType($status);

    /**
     * Get status of auto generate content type
     *
     * @return bool
     */
    public function getAutoContentType();

    /**
     * Set content data compiler
     *
     * @param string|ContentDataCompiler\CompilerInterface $compiler
     */
    public function setContentDataCompiler($compiler);

    /**
     * Get content data compiler
     *
     * @return mixed
     */
    public function getContentDataCompiler();

    /**
     * Set Http version
     *
     * @param string $version
     */
    public function setHttpVersion($version);

    /**
     * Get Http version
     *
     * @return string
     */
    public function getHttpVersion();

    /**
     * Prepare headers after request
     */
    public function prepare();
}