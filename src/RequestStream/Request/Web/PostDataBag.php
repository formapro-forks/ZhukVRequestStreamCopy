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
    public function add($offset, $value)
    {
        if (!$value instanceof PostData) {
            $value = new PostData($offset, $value);
        }

        // Reset boundary
        $this->boundary = null;

        return parent::add($offset, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($offset)
    {
        // Reset boundary
        $this->boundary = null;

        return parent::remove($offset);
    }

    /**
     * Generate boundary
     *
     * @return string
     */
    public function generateBoundary()
    {
        if ($this->boundary) {
            return $this->boundary;
        }

        return $this->boundary = md5(serialize($this->_storage));
    }

    /**
     * Get minimize string
     *
     * @return string
     */
    public function getMinimizeString()
    {
        $result = array();

        foreach ($this->all() as $post) {
            /** @var PostData $post */
            $result[] = urlencode($post->getName()) . '=' . urlencode($post->getValue());
        }

        return implode('&', $result);
    }

    /**
     * __toString
     */
    public function __toString()
    {
        $postData = "\r\n";

        foreach ($this->_storage as $postValue) {
            $postData .= '--' . $this->generateBoundary() . "\n" .
                ((string) $postValue) . "\r\n";
        }

        $postData .= '--' . $this->generateBoundary() . '--';

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