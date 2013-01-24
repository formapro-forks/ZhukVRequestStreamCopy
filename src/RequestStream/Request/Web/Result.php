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

use RequestStream\Request\Exception\ResultException;


/**
 * Base core for control result web request
 */
class Result implements ResultInterface
{
    /**
     * @var string
     */
    protected $data;

    /**
     * @var HeadersBag
     */
    protected $headers;

    /**
     * @var integer
     */
    protected $code;

    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var CookiesBag
     */
    protected $cookies;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->headers = new HeadersBag;
        $this->cookies = new CookiesBag;
    }

    /**
     * Get data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get headers
     *
     * @param string $name
     */
    public function getHeaders($name = NULL)
    {
        return $name ? (isset($this->headers[$name]) ? $this->headers[$name] : NULL) : $this->headers;
    }

    /**
     * Get code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get cookie
     *
     * @param string $name
     */
    public function getCookies($name = NULL)
    {
        return $name ? (isset($this->cookies[$name]) ? $this->cookies[$name] : NULL) : $this->cookies;
    }

    /**
     * Is cookie
     *
     * @param string $name
     */
    public function isCookie($name = NULL)
    {
        return $name ? isset($this->cookies[$name]) : (bool) $this->cookies;
    }

    /**
     * Parse page content
     *
     * @param string $content
     */
    public function parsePageContent($content)
    {
        $content = explode("\r\n\r\n", $content, 2);

        if (!count($content)) {
          throw new ResultException('Can\'t parse page content. Not found header or page section.');
        }

        $this->parsePageHeaders($content[0]);

        $this->data = @$content[1];

    }

    /**
     * Parse headers
     *
     * @param string $headerContent
     */
    protected function parsePageHeaders($headerContent)
    {
        $headers = preg_split("/\r\n|\r|\n/", $headerContent);

        @list ($this->protocol, $this->code, $text) = explode(' ', $headers[0]);
        unset ($headers[0]);

        $responses = array(
            100 => 'Continue', 101 => 'Switching Protocols',
            200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
            300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect',
            400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed',
            500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported'
        );

        $this->response = $responses[$this->code];

        foreach ($headers as $h) {
            list ($key, $value) = explode(':', $h, 2);

            if (strtolower($key) == 'set-cookie') {
                $this->cookies->add(Cookie::parseFromString($value), NULL);
            }
            else {
                $this->headers[trim($key)] = trim($value);
            }
        }
    }
}