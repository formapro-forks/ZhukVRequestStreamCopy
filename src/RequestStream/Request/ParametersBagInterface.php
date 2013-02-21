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
 * Interface for control parameters bag
 */
interface ParametersBagInterface extends \Iterator, \Countable, \ArrayAccess
{
    /**
     * Add parameter to bag
     *
     * @param string $name
     * @param mixed $value
     */
    public function add($name, $value);

    /**
     * Has parameter in bag
     *
     * @param string $name
     */
    public function has($name);

    /**
     * Remove parameter from bag
     *
     * @param string $name
     */
    public function remove($name);

    /**
     * Get all parameters
     */
    public function all();

    /**
     * Remove all parameters
     */
    public function removeAll();
}