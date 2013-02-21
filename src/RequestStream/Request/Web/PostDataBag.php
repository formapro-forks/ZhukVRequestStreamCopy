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

use RequestStream\Request\ParametersBag;

/**
 * POST data collection
 */
class PostDataBag extends ParametersBag
{
    /**
     * @var string
     */
    protected $boundary;

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof PostData) {
            $value = new PostData($offset, $value);
        }

        // Reset boundary
        $this->boundary = null;

        return parent::offsetSet($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        // Reset boundary
        $this->boundary = null;

        return parent::offsetUnset($offset);
    }

    /**
     * Generate boundary
     *
     * @return string
     */
    public function generateBundary()
    {
        if ($this->boundary) {
            return $this->boundary;
        }

        return $this->boundary = md5(serialize($this->_storage));
    }

    /**
     * __toString
     */
    public function __toString()
    {
        $postData = "\r\n";

        foreach ($this->_storage as $postKey => $postValue) {
            $postData .= '--' . $this->generateBundary() . "\n" .
                ((string) $postValue) . "\r\n";
        }

        $postData .= '--' . $this->generateBundary() . '--';

        return $postData;
    }

    /**
     * Get size
     *
     * @return integer
     */
    public function getContentLength()
    {
        return mb_strlen($this->__toString());
    }
}