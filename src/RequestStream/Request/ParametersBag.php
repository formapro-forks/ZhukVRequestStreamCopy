<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
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
        return $this->has($offset);
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * Implements \ArrayAccess
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * {@inheritDoc}
     */
    public function get($name)
    {
        return $this->_storage[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function add($name, $value)
    {
        $this->_storage[$name] = $value;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has($name)
    {
        return isset($this->_storage[$name]);
    }

    /**
     * {@inheritDoc}
     */
    public function remove($name)
    {
        unset ($this->_storage[$name]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removeAll()
    {
        $this->_storage = array();
    }

    /**
     * {@inheritDoc}
     */
    public function all()
    {
        return $this->_storage;
    }
}