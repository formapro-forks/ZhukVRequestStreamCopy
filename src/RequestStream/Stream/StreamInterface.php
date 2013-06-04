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
 * Interface for control request response
 */
interface StreamInterface
{
    /**
     * Get stream transport
     *
     * @return array
     */
    public static function getTransports();

    /**
     * Is validate transport in system
     *
     * @param string $transport
     *
     * @return bool
     */
    public static function isTransport($transport);

    /**
     * Get all allowed wrappers in system
     *
     * @return array
     */
    public static function getWrappers();

    /**
     * Is wrapper allowed in system
     *
     * @param string $wrapper
     *
     * @return bool
     */
    public static function isWrapper($wrapper);

    /**
     * Get full path file, included all paths
     *
     * @param string $path
     *
     * @return string
     */
    public static function resolveIncludePath($path);

    /**
     * Select status streams
     *
     * @see http://php.net/manual/en/function.stream-select.php
     *
     * @param array &$read
     * @param array &$write
     * @param array &$except
     * @param integer $seconds
     * @param integer $uSec
     */
    public static function select(array &$read = null, array &$write = null, array &$except = null, $sec = 1, $uSec = 0);

    /**
     * Is created stream
     *
     * @param bool $autoload
     *
     * @return bool
     */
    public function is($autoload = false);

    /**
     * Create stream
     */
    public function create();

    /**
     * Get resource of stream.
     *
     * @return resource
     */
    public function getResource();
}