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
 * Cookies collection
 */
class CookiesBag extends ParametersBag
{
    /**
     * {@inheritDoc}
     */
    public function add($name, $value)
    {
        if ($name instanceof Cookie) {
            $value = $name;
            $name = $name->getName();
        } else if ($value instanceof Cookie) {
            $name = $value->getName();
        } else {
            $value = new Cookie($name, $value);
        }

        return parent::add($name, $value);
    }

    /**
     * __toString
     */
    public function __toString()
    {
        $cookies = array();

        foreach ($this->all() as $cookie) {
            $cookies[] = (string) $cookie;
        }

        return implode('; ', $cookies);
    }
}