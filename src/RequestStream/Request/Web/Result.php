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
    public $headers;

    /**
     * @var integer
     */
    protected $code;

    /**
     * @var string
     */
    protected $protocol;

    /**
     * @var CookiesBag
     */
    public $cookies;

    /**
     * @var float
     */
    public $requestTime;

    /**
     * Construct
     */
    public function __construct($code = 200,  $data = NULL, $protocol = NULL, HeadersBag $headers = NULL, CookiesBag $cookies = NULL, $requestTime = NULL)
    {
        $this->code = $code;
        $this->data = $data;
        $this->protocol = $protocol;
        $this->headers = $headers === NULL ? new HeadersBag : $headers;
        $this->cookies = $cookies === NULL ? new CookiesBag : $cookies;
        $this->requestTime = $requestTime;
    }

    /**
     * @{inerhitDoc}
     */
    public function getData()
    {
        return $this->data;
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @{inerhitDoc}
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * @{inerhitDoc}
     */
    public function getCookies($name = NULL)
    {
        return $this->cookies;
    }

    /**
     * @{inerhitDoc}
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * @{inerhitDoc}
     */
    static public function parseFromContent($content, $requestTime = NULL)
    {
        $content = explode("\r\n\r\n", $content, 2);

        if (!count($content)) {
          throw new ResultException('Can\'t parse page content. Not found header or page section.');
        }

        $info = self::parsePageHeaders($content[0]);

        return new static(
            (int) $info['code'],
            @$content[1],
            $info['protocol'],
            $info['headers'],
            $info['cookies'],
            $requestTime
        );
    }

    /**
     * @{inerhitDoc}
     */
    protected static function parsePageHeaders($headerContent)
    {
        $result = array(
            'protocol' => NULL,
            'code' => NULL,
            'headers' => new HeadersBag,
            'cookies' => new CookiesBag
        );

        $headers = preg_split("/\r\n|\r|\n/", $headerContent);

        @list ($result['protocol'], $result['code'], $text) = explode(' ', $headers[0]);
        unset ($headers[0]);

        foreach ($headers as $h) {
           list ($key, $value) = explode(':', $h, 2);

            if (strtolower($key) == 'set-cookie') {
                $result['cookies']->add(Cookie::parseFromString($value), NULL);
            }
            else {
                $result['headers']->add(trim($key), trim($value, '" '));
            }
        }

        return $result;
    }
}