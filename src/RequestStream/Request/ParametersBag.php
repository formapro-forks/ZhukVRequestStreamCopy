<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request;

/**
 * Default parameters bag
 */
class ParametersBag implements ParametersBagInterface
{
    /**
     * @var array
     */
    protected $_storage = array();

    /**
     * Construct
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * Implements \Iterator
     */
    public function current()
    {
        return current($this->_storage);
    }

    /**
     * Implements \Iterator
     */
    public function next()
    {
        return next($this->_storage);
    }

    /**
     * Implements \Iterator
     */
    public function key()
    {
        return key($this->_storage);
    }

    /**
     * Implements \Iterator
     */
    public function valid()
    {
        return $this->current();
    }

    /**
     * Implements \Iterator
     */
    public function rewind()
    {
        return reset($this->_storage);
    }

    /**
     * Implements \Countable
     */
    public function count()
    {
        return count($this->_storage);
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetExists($offset)
    {
        return isset($this->_storage[$offset]);
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->_storage[$offset];
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $this->_storage[$offset] = $value;
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetUnset($offset)
    {
        unset ($this->_storage[$offset]);
    }

    /**
     * @{inerhitDoc}
     */
    public function add($name, $value)
    {
        return $this->offsetSet($name, $value);
    }

    /**
     * @{inerhitDoc}
     */
    public function has($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @{inerhitDoc}
     */
    public function remove($name)
    {
        return $this->offsetUnset($name);
    }

    /**
     * @{inerhitDoc}
     */
    public function removeAll()
    {
        $this->_storage = array();
    }

    /**
     * @{inerhitDoc}
     */
    public function all()
    {
        return $this->_storage;
    }
}