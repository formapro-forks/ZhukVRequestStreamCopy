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
    protected $requestTime;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Construct
     */
    public function __construct(RequestInterface $request, $code = 200,  $data = null, $protocol = null, HeadersBag $headers = null, CookiesBag $cookies = null, $requestTime = null)
    {
        $this->code = $code;
        $this->data = $data;
        $this->protocol = $protocol;
        $this->headers = $headers === null ? new HeadersBag : $headers;
        $this->cookies = $cookies === null ? new CookiesBag : $cookies;
        $this->requestTime = $requestTime;
        $this->request = $request;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritDoc}
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * {@inheritDoc}
     */
    public function getCookies($name = null)
    {
        return $this->cookies;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestTime()
    {
        return $this->requestTime;
    }

    /**
     * {@inheritDoc}
     */
    public static function parseFromContent(RequestInterface $request, $content, $requestTime = null)
    {
        $content = explode("\r\n\r\n", $content, 2);

        if (!count($content)) {
            throw new ResultException('Can\'t parse page content. Not found header or page section.');
        }

        $info = self::parsePageHeaders($content[0]);

        return new static(
            $request,
            (int) $info['code'],
            @$content[1],
            $info['protocol'],
            $info['headers'],
            $info['cookies'],
            $requestTime
        );
    }

    /**
     * {@inheritDoc}
     */
    protected static function parsePageHeaders($headerContent)
    {
        $result = array(
            'protocol' => null,
            'code' => null,
            'headers' => new HeadersBag,
            'cookies' => new CookiesBag
        );

        $headers = preg_split("/\r\n|\r|\n/", $headerContent);

        @list ($result['protocol'], $result['code'], $text) = explode(' ', $headers[0]);
        unset ($headers[0]);

        foreach ($headers as $h) {
            list ($key, $value) = explode(':', $h, 2);

            if (strtolower($key) == 'set-cookie') {
                $result['cookies']->add(Cookie::parseFromString($value), null);
            } else if (strtolower($key) == 'content-type') {
                // Parse content type. Example: Content-Type: application/xml; charset=UTF-8
                $contentType = explode(';', $value);
                $result['headers']->add('Content-Type', trim($contentType[0]));

                if (isset($contentType[1])) {
                    // Charset already exists
                    $charset = explode('=', $contentType[1]);
                    $result['headers']->add('Charser', isset($charset[1]) ? trim($charset[1]) : '');
                }
            } else {
                $result['headers']->add(trim($key), trim($value, '" '));
            }
        }

        return $result;
    }
}