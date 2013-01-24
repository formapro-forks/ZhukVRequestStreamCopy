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
    public function __construct($host, $path = '/', $port = NULL, $secure = FALSE, array $query = array(), $fragment = NULL, array $userLogin = array())
    {
        $this->host = $host;
        $this->path = $path;
        $this->port = $port;
        $this->secure = (bool) $secure;
        $this->query = $query;
        $this->fragment = $fragment;
        $this->userLogin = $userLogin + array('user' => NULL, 'password' => NULL);
    }

    /**
     * Parse from string
     *
     * @param string $url
     *
     * @return Uri
     */
    static public function parseFromString($url)
    {
        if (preg_match('/^([a-z]{0,5}):\/\//', $url, $tmp)) {
            if (!in_array($tmp[1], array('http', 'https'))) {
                throw new UriException(sprintf('Uri must be beginning from "http" or "https" (Beginning with: "%s")', $tmp[1]));
            }
        }
        else {
            $url = 'http://' . $url;
        }

        if (!$parseUri = @parse_url($url)) {
            throw new \InvalidArgumentException(sprintf('Can\'t parse uri "%s". Please check uri!', $uri));
        }

        if (strpos($parseUri['host'], '.') === FALSE) {
            throw new \InvalidArgumentException(sprintf('Can\'t parse uri "%s". Undefined host.', $uri));
        }

        if (isset($parseUri['query'])) {
            $parseUri['query'] = explode('&', $parseUri['query']);
        }

        $parseUri += array(
            'user' => NULL,
            'pass' => NULL,
            'fragment' => NULL,
            'query' => array(),
            'port' => NULL,
            'path' => '/'
        );

        $args = array(
            $parseUri['host'],
            $parseUri['path'],
            $parseUri['port'],
            $parseUri['scheme'] === 'https' ? TRUE : FALSE,
            $parseUri['query'],
            $parseUri['fragment']
        );

        if ($parseUri['user']) {
            $args[] = array('user' => $parseUri['user'], 'password' => @$parseUri['pass']);
        }

        $refClass = new \ReflectionClass(__CLASS__);
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
        return ($this->secure ? 'https' : 'http') .
            '://' .
            ($this->userLogin['user'] ? $this->userLogin['user'] . ':' . $this->userLogin['password'] . '@' : '') .
            $this->host .
            ($this->port ? ':' . $this->port : '') .
            $this->path .
            ($this->query ? '?' . implode('&', $this->query) : '') .
            ($this->fragment ? '#' . $this->fragment : '');
    }
}