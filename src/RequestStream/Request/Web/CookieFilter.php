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
     * @param \Iterator $iterator
     * @param string $path
     * @param string $domain
     * @param \DateTime $expires
     * @param bool $secure
     * @param bool $httpOnly
     */
    public function __construct(\Iterator $iterator, $path = null, $domain = null, \DateTime $expires = null, $secure = null, $httpOnly = null)
    {
        parent::__construct($iterator);

        $this->path = $path;
        $this->domain = $domain;
        $this->expires = $expires;
        $this->secure = $secure === null ? null : (bool) $secure;
        $this->httpOnly = $secure === null ? null : (bool) $httpOnly;
    }

    /**
     * {@inheritDoc}
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

        if ($this->httpOnly !== null && $this->httpOnly != $cookie->getHttpOnly()) {
            return false;
        }

        if ($this->secure !== null && $this->secure != $cookie->getSecure()) {
            return false;
        }

        return true;
    }
}