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
 * Cookie class
 */
class Cookie
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var \DateTime
     */
    protected $expires;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var boolean secure
     */
    protected $secure;

    /**
     * @var boolean
     */
    protected $httpOnly;

    /**
     * Create new cookie object
     *
     * @param string $name
     * @param string $value
     * @param \DateTime $expires
     * @param string $path
     * @param string $domain
     * @param boolean $secure
     * @param boolean $httpOnly
     */
    public function __construct($name, $value, \DateTime $expires = null, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->path = $path ? $path : '/';
        $this->domain = $domain;
        $this->secure = (bool) $secure;
        $this->httpOnly = (bool) $httpOnly;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get expires
     *
     * @return \DateTime|null
     */
    public function getExpires()
    {
        return $this->expires;
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
     * Get domain
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
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
     * Get http only status
     *
     * @return boolean
     */
    public function getHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Parse cookie from string
     *
     * @param $cookieStr
     * @return Cookie
     */
    static public function parseFromString($cookieStr)
    {
        // Get base values from cookie string
        @list ($value, $otherData) = explode(';', $cookieStr, 2);

        // Get name and value
        list ($name, $value) = explode('=', $value);

        // TODO: Control another options in cookie
        return new static(
            trim($name),
            trim($value)
        );
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return $this->getName() . '=' . $this->getValue();
    }
}