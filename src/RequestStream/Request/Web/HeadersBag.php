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
 * Headers collection
 */
class HeadersBag extends ParametersBag
{
    /**
     * {@inheritDoc}
     */
    public function add($name, $value)
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

        return parent::add($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        return parent::get($this->searchKey($name));
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return parent::has($this->searchKey($name));
    }

    /**
     * {@inheritDoc}
     */
    public function remove($name)
    {
        return parent::remove($this->searchKey($name));
    }

    /**
     * Search key
     *
     * @param string $key
     * @return string
     */
    protected function searchKey($key)
    {
        // Key already exists
        if (isset($this->_storage[$key])) {
            return $key;
        }

        // Lower all keys and check equals
        foreach ($this->all() as $keyName => $value) {
            if ($keyName != $key) {
                if (mb_strtolower($keyName) == mb_strtolower($key)) {
                    return $keyName;
                }
            }
        }

        return $key;
    }

    /**
     * __toString
     */
    public function __toString()
    {
        $headerStr = '';

        foreach ($this->all() as $headerName => $headerValue) {
            $headerStr .= $headerName . ': ' . $headerValue . "\n";
        }

        return rtrim($headerStr);
    }
}