<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream;

/**
 * Interface for control context
 */
interface ContextInterface
{
    /**
     * Get default options for content
     *
     * @return array
     */
    public static function getAllowedOptionsContext();

    /**
     * Get default context
     *
     * @return resource
     */
    public static function getDefault();

    /**
     * Get params from content
     *
     * @param resource $streamOrContent
     *
     * @return array
     */
    public function getParams($streamOrContext = null);

    /**
     * Get options from content
     *
     * @param resource $streamOrContext
     *
     * @return array
     */
    public function getOptions($streamOrContext = null);

    /**
     * Set options to context
     *
     * @param string $wrapper
     * @param string $paramName
     * @param string $paramValue
     *
     * @return null
     */
    public function setOptions($wrapper, $paramName = null,  $paramValue = null);

    /**
     * Set params to context
     *
     * @param array $params
     */
    public function setParams(array $params);

    /**
     * Is created content
     *
     * @param bool $autoload
     * @param array $options
     */
    public function is($autoload = false, array $options = array());

    /**
     * Create stream of context
     *
     * @param array $options
     * @param array $params
     */
    public function create(array $options = array(), array $params = array());
}