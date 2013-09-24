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
 * HTTP Client
 */
class Client
{
    /**
     * @var string
     */
    private static $defaultConnectionType = 'socket';

    /**
     * Set default connection type
     *
     * @param string $connectionType
     * @throws \InvalidArgumentException
     */
    public static function setDefaultConnectionType($connectionType)
    {
        $connectionType = mb_strtolower($connectionType);

        if (!in_array($connectionType, array('socket', 'curl'))) {
            throw new \InvalidArgumentException(sprintf(
                'Connection type "%s" is not allowed. Available: "socket" and "curl".',
                $connectionType
            ));
        }

        self::$defaultConnectionType = $connectionType;
    }

    /**
     * Create new connection
     *
     * @param string $type
     * @throws \InvalidArgumentException
     * @return WebInterface
     */
    public static function createConnection($type = null)
    {
        if (null === $type) {
            $type = self::$defaultConnectionType;
        }

        switch (mb_strtolower($type)) {
            case 'curl':
                return new Curl\Connection();

            case 'socket':
                return new Socket\Connection();

            default:
                throw new \InvalidArgumentException(sprintf(
                    'Connection type "%s" is not allowed. Available: "socket" and "curl".',
                    $type
                ));
        }
    }

    /**
     * Create a new request instance
     *
     * @param string $method
     * @param string $uri
     * @param mixed $content
     * @param array $headers
     * @param array $cookies
     * @param array $options
     * @return RequestInterface
     */
    public static function createRequest($method, $uri, $content = null, array $headers = array(), array $cookies = array(), array $options = array())
    {
        $options += array(
            'content_data_compiler' => null
        );

        if ('POST' === $method) {
            $request = new PostRequest();

            if (null === $content) {
                $content = array();
            }

            $request->setPostData($content);
        } else {
            $request = new DefaultRequest();
            $request
                ->setMethod($method)
                ->setContentData($content, $options['content_data_compiler']);
        }

        $request
            ->setUri($uri)
            ->setHeaders($headers)
            ->setCookies($cookies);

        return $request;
    }

    /**
     * Send request
     *
     * @param string $method
     * @param string $uri
     * @param mixed $content
     * @param array $headers
     * @param array $cookies
     * @param array $options
     * @return ResultInterface
     */
    public static function request($method, $uri, $content = null, array $headers = array(), array $cookies = array(), array $options = array())
    {
        $connection = self::createConnection();
        $request = self::createRequest($method, $uri, $content, $headers, $cookies, $options);

        $connection->setRequest($request);

        return $connection->getResult();
    }

    /**
     * Send GET request
     *
     * @param string $uri
     * @param mixed $content
     * @param array $headers
     * @param array $cookies
     * @param array $options
     * @return ResultInterface
     */
    public static function get($uri, $content = null, array $headers = array(), array $cookies = array(), array $options = array())
    {
        $connection = self::createConnection();
        $request = self::createRequest('GET', $uri, $content, $headers, $cookies, $options);

        $connection->setRequest($request);

        return $connection->getResult();
    }

    /**
     * Send POST request
     *
     * @param string $uri
     * @param array|\Traversable|PostDataBag $content
     * @param array $headers
     * @param array $cookies
     * @param array $options
     * @return ResultInterface
     */
    public static function post($uri, $content = array(), array $headers = array(), array $cookies = array(), array $options = array())
    {
        $connection = self::createConnection();
        $request = self::createRequest('POST', $uri, $content, $headers, $cookies, $options);

        $connection->setRequest($request);

        return $connection->getResult();
    }
}