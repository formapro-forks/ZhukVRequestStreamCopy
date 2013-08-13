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
 * Abstract core for use stream
 */
abstract class StreamAbstract implements StreamInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * {@inheritDoc}
     */
    public static function getTransports()
    {
        return stream_get_transports();
    }

    /**
     * {@inheritDoc}
     */
    public static function isTransport($transport)
    {
        return in_array($transport, self::getTransports());
    }

    /**
     * {@inheritDoc}
     */
    public static function getWrappers()
    {
        return stream_get_wrappers();
    }

    /**
     * {@inheritDoc}
     */
    public static function isWrapper($wrapper)
    {
        return in_array($wrapper, self::getWrappers());
    }

    /**
     * {@inheritDoc}
     */
    public static function resolveIncludePath($file)
    {
        return stream_resolve_include_path($file);
    }

    /**
     * {@inheritDoc}
     */
    public static function select(array &$read = null, array &$write = null, array &$except = null, $sec = 1, $uSec = 0)
    {
        if (!$read && !$write && !$except) {
            throw new \InvalidArgumentException('Not found streams for select.');
        }

        return stream_select($read, $write, $except, $sec, $uSec);
    }

    /**
     * {@inheritDoc}
     */
    public function is($autoload = false)
    {
        if (!$autoload) {
            return (bool) $this->resource;
        }

        try{
            $this->create();
        } catch (\Exception $e) {
            return false;
        }

        return (bool) $this->resource;
    }

    /**
     * {@inheritDoc}
     */
    public function getResource()
    {
        if ($this->resource === null) {
            $this->create();
        }

        return $this->resource;
    }
}