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
 * Cookies collection
 */
class CookiesBag extends ParametersBag
{
    /**
     * @var array
     */
    protected $_storageReal = array();

    /**
     * @{inerhitDoc}
     */
    public function offsetSet($name, $value)
    {
        if ($name instanceof Cookie) {
            $value = $name;
            $name = $name->getName();
        } else if ($value instanceof Cookie) {
            $name = $value->getName();
        } else {
            $value = new Cookie($name, $value);
        }

        $this->_storageReal[$name] = $value;

        return parent::offsetSet($name, $value);
    }

    /**
     * @{inerhitDoc}
     */
    public function offsetGet($name)
    {
        return parent::offsetGet(mb_strtolower($name));
    }

    /**
     * @{inerhitDoc}
     */
    public function offsetExists($name)
    {
        return parent::offsetExists(strtolower($name));
    }

    /**
     * @{inerhitDoc}
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
        $cookies = array();

        foreach ($this->_storageReal as $cookie) {
            $cookies[] = (string) $cookie;
        }

        return implode('; ', $cookies);
    }
}