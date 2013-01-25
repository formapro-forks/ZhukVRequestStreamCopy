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
 * Cookie class
 */
class Cookie
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var value
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
    public function __construct($name, $value, \DateTime $expires = NULL, $path = NULL, $domain = NULL, $secure = FALSE, $httpOnly = TRUE)
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
     * @return \DateTime|NULL
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
     * @param string $cookie
     *
     * @return Cookie
     */
    static public function parseFromString($cookieStr)
    {
        // Get base values from cookie string
        @list ($value, $expires, $path, $domain, $secure, $httpOnly) = explode(';', $cookieStr);

        // Get name, value, path etc... from cookie item
        @list ($name, $value) = explode('=', trim($value));
        @list ($null, $expires) = explode('=', trim($expires));
        @list ($null, $path) = explode('=', trim($path));
        @list ($null, $domain) = explode('=', trim($domain));

        // If not added expires to set cookie
        if ($expires == '/') {
            $expires = NULL; $path = '/';
        }

        return new static(
            $name,
            $value,
            $expires ? new \DateTime($expires) : NULL,
            $path,
            $domain,
            $secure,
            $httpOnly === NULL ? TRUE : $httpOnly
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