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

use RequestStream\Request\Exception\UriException;

/**
 * URI Core
 */
class Uri
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var boolean
     */
    protected $secure;

    /**
     * @var array
     */
    protected $query;

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @var array
     */
    protected $userLogin;

    /**
     * Construct
     */
    public function __construct($host, $path = '/', $port = null, $secure = false, array $query = array(), $fragment = null, array $userLogin = array())
    {
        $this->host = $host;
        $this->path = $path;
        $this->port = $port;
        $this->secure = (bool) $secure;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->userLogin = $userLogin + array('user' => null, 'password' => null);
    }

    /**
     * Parse from string
     *
     * @param string $url
     * @throws \RequestStream\Request\Exception\UriException
     * @return Uri
     */
    public static function parseFromString($url)
    {
        if (preg_match('/^([a-z]{0,5}):\/\//', $url, $tmp)) {
            if (!in_array($tmp[1], array('http', 'https'))) {
                throw new UriException(sprintf('Uri must be beginning from "http" or "https" (Beginning with: "%s")', $tmp[1]));
            }
        } else {
            $url = 'http://' . $url;
        }

        if (!$parseUri = @parse_url($url)) {
            throw new UriException(sprintf('Can\'t parse uri "%s". Please check uri!', $url));
        }

        if (strpos($parseUri['host'], '.') === FALSE) {
            throw new UriException(sprintf('Can\'t parse uri "%s". Undefined host.', $url));
        }

        if (isset($parseUri['query'])) {
            $parseUri['query'] = explode('&', $parseUri['query']);
        }

        $parseUri += array(
            'user' => null,
            'pass' => null,
            'fragment' => null,
            'query' => array(),
            'port' => null,
            'path' => '/'
        );

        $args = array(
            $parseUri['host'],
            $parseUri['path'],
            $parseUri['port'],
            $parseUri['scheme'] === 'https' ? true : false,
            $parseUri['query'],
            $parseUri['fragment']
        );

        if ($parseUri['user']) {
            $args[] = array('user' => $parseUri['user'], 'password' => @$parseUri['pass']);
        }

        $refClass = new \ReflectionClass(get_called_class());
        return $refClass->newInstanceArgs($args);
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Get secure
     *
     * @return boolean
     */
    public function getSecure()
    {
        return $this->secure;
    }

    /**
     * Get query
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Get fragment
     *
     * @return string
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Get user login
     *
     * @return array
     */
    public function getUserLogin()
    {
        return $this->userLogin;
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return $this->getDomain() .
            $this->path .
            ($this->query ? '?' . implode('&', $this->query) : '') .
            ($this->fragment ? '#' . $this->fragment : '');
    }

    /**
     * Get domain with scheme
     *
     * @return string
     */
    public function getDomain()
    {
        return ($this->secure ? 'https' : 'http') .
            '://' .
            ($this->userLogin['user'] ? $this->userLogin['user'] . ':' . $this->userLogin['password'] . '@' : '') .
            $this->host .
            ($this->port ? ':' . $this->port : '');
    }
}