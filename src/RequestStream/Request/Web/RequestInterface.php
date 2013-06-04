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
     * @param HeadersBag $headers
     */
    public function setHeaders(HeadersBag $headers);

    /**
     * Get headers bag
     *
     * @return HeadersBag
     */
    public function getHeaders();

    /**
     * Set cookies
     *
     * @param CookiesBag $cookies
     */
    public function setCookies(CookiesBag $cookies);

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