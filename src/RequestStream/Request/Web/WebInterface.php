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
 * Interface for control web request
 */
interface WebInterface
{
    /**
     * Set proxy
     *
     * @param string|Proxy $proxy
     */
    public function setProxy($proxy);

    /**
     * Get proxy
     *
     * @return string
     */
    public function getProxy();

    /**
     * Set method foe request (GET, POST, HEAD, etc...)
     *
     * @param string $method
     */
    public function setMethod($method);

    /**
     * Get used method
     *
     * @return string
     */
    public function getMethod();

    /**
     * Set user agent
     *
     * @param string $userAgent
     */
    public function setUserAgent($userAgent);

    /**
     * Get used user agent
     *
     * @return string
     */
    public function getUserAgent();

    /**
     * Set random user agent
     *    Generate user agent
     *
     * @param string $browser
     */
    public function setUserAgentRandom($browser);

    /**
     * Set cookies for request
     *
     * @param array|Iterator $cookies
     */
    public function setCookies($cookie);

    /**
     * Set item cookie
     *
     * @param string $name
     * @param string $value
     */
    public function addCookie($name, $value = NULL);

    /**
     * Delete cookie from request
     *
     * @param string $name
     */
    public function deleteCookie($name);

    /**
     * Clear all cookies
     */
    public function clearCookies();

    /**
     * Set post data
     *
     * @param array $data
     */

    public function setPostData(array $data);

    /**
     * Add one item to post data
     *
     * @param string|array $name
     * @param mixed $value
     */
    public function addPostData($name, $value = NULL);

    /**
     * Delete post data
     *
     * @param string $name
     */
    public function deletePostData($name = NULL);

    /**
     * Set XML data
     *
     * @param string|DOMDocument $xmlData
     */
    public function setXmlData($xmlData);

    /**
     * Get XML data
     *
     * @return DOMDocument
     */
    public function getXmlData();

    /**
     * Get XML Data as string
     *
     * @return string
     */
    public function getXmlDataString();

    /**
     * Validate XML && Post data
     */
    public function validateXmlPostData();

    /**
     * Set headers
     *
     * @param array|Itarator|HeadersBag $headers
     */
    public function setHeaders($headers);

    /**
     * Add header
     *
     * @param string|array $name
     * @param mixed $value
     */
    public function addHeader($name, $value = NULL);

    /**
     * Get headers
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getHeaders($name = NULL);

    /**
     * Delete headers
     *
     * @param string $name
     */
    public function deleteHeader($name = NULL);

    /**
     * Set uri for request
     *
     * @param string $uri
     */
    public function setUri($uri);

    /**
     * Set HTTP Version protocol
     *
     * @param string $version
     */
    public function setHttpVersion($version);

    /**
     * Get HTT Version
     *
     * @return string
     */
    public function getHttpVersion();

    /**
     * Set referer for request
     *
     * @param string $uri
     */
    public function setReferer($uri);

    /**
     * Set max count redirect
     *
     * @param int $count
     */
    public function setCountRedirect($count = 0);

    /**
     * Set using cookie in redirected
     *
     * @param bool $status
     */
    public function setRedirectCookie($status = TRUE);

    /**
     * Send request
     *
     * @param bool $reset
     *
     * @return Result
     */
    public function sendRequest($reset = FALSE);

    /**
     * Get result request
     *
     * @param bool $reset
     *
     * @return Result
     */
    public function getResult($reset = FALSE);
}
