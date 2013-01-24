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
 * Cookie filters
 */
class CookieFilter extends \FilterIterator
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string $expires
     */
    protected $expires;

    /**
     * @var boolean
     */
    protected $secure;

    /**
     * @var boolean
     */
    protected $httpOnly;

    /**
     * Construct
     *
     * @param
     * @param string $path
     * @param string $domain
     * @param \DateTime $expires
     */
    public function __construct(\Iterator $iterator, $path = NULL, $domain = NULL, \DateTime $expires = NULL, $secure = NULL, $httpOnly = NULL)
    {
        parent::__construct($iterator);

        $this->path = $path;
        $this->domain = $domain;
        $this->expires = $expires;
        $this->secure = $secure === NULL ? NULL : (bool) $secure;
        $this->httpOnly = $secure === NULL ? NULL : (bool) $httpOnly;
    }

    /**
     * @{inerhitDoc}
     */
    public function accept()
    {
        $cookie = $this->getInnerIterator()->current();

        if (!$cookie instanceof Cookie) {
            throw new \LogicException(sprintf(
                'Cookie must be "Cookie" object, "%s" given.',
                is_object($cookie) ? get_class($cookie) : gettype($cookie)
            ));
        }

        if ($this->httpOnly !== NULL && $this->httpOnly != $cookie->getHttpOnly()) {
            return FALSE;
        }

        if ($this->secure !== NULL && $this->secure != $cookie->getSecure()) {
            return FALSE;
        }

        return TRUE;
    }
}