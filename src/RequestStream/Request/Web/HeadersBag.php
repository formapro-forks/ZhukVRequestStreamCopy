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

use RequestStream\Request\ParametersBag;

/**
 * Headers collection
 */
class HeadersBag extends ParametersBag
{
    /**
     * @var array
     */
    protected $_storageReal = array();

    /**
     * {@inheritDoc}
     */
    public function offsetSet($name, $value)
    {
        if (is_object($value) && !$value instanceof CookiesBag) {
            if (!method_exists($value, '__toString')) {
                throw new \InvalidArgumentException(sprintf(
                    'Can\'t set header "%s". Not found __toString method in object "%s".',
                    $name, get_class($value)
                ));
            }

            $value = (string) $value;
        }

        if (!is_string($value) && !is_numeric($value) && !$value instanceof CookiesBag) {
            throw new \InvalidArgumentException(sprintf(
                'Header must be a string, "%s" given',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        $nameLower = mb_strtolower($name);

        if ($nameLower == 'cookie') {
            if (!$value instanceof CookiesBag) {
                throw new \InvalidArgumentException(sprintf(
                    'Cookies must be CookiesBag, "%s" given.',
                    is_object($value) ? get_class($value) : gettype($value)
                ));
            }
        }

        if ($nameLower == 'referer' && !$value instanceof Uri) {
            $value = Uri::parseFromString($value);
        }

        $this->_storageReal[$name] = $value;

        return parent::offsetSet($nameLower, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($name)
    {
        return parent::offsetGet(mb_strtolower($name));
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($name)
    {
        return parent::offsetExists(strtolower($name));
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($name)
    {
        unset ($this->_storageReal[$name]);
        parent::offsetUnset(strtolower($name));
    }

    /**
     * __toString
     */
    public function __toString()
    {
        $headerStr = '';

        foreach ($this->_storageReal as $headerName => $headerValue) {
            $headerStr .= $headerName . ': ' . $headerValue . "\n";
        }

        return rtrim($headerStr);
    }
}