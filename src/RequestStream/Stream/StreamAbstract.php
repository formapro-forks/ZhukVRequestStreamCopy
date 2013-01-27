<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
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
    protected $resource = NULL;

    /**
     * @{inerhitDoc}
     */
    public static function getTransports()
    {
        return stream_get_transports();
    }

    /**
     * @{inerhitDoc}
     */
    public static function isTransport($transport)
    {
        return in_array($transport, self::getTransports());
    }

    /**
     * @{inerhitDoc}
     */
    public static function getWrappers()
    {
        return stream_get_wrappers();
    }

    /**
     * @{inerhitDoc}
     */
    public static function isWrapper($wrapper)
    {
        return in_array($wrapper, self::getWrappers());
    }

    /**
     * @{inerhitDoc}
     */
    public static function resolveIncludePath($file)
    {
        return stream_resolve_include_path($file);
    }

    /**
     * @{inerhitDoc}
     */
    public static function select(array &$read = NULL, array &$write = NULL, array &$except = NULL, $sec = 1, $usec = 0)
    {
        if (!$read && !$write && !$except) {
            throw new \InvalidArgumentException('Not found streams for select.');
        }

        return stream_select($read, $write, $except, $sec, $usec);
    }

    /**
     * @{inerhitDoc}
     */
    public function is($autoload = FALSE)
    {
        if (!$autoload) {
            return (bool) $this->resource;
        }

        try{
            $this->create();
        }
        catch (\Exception $e) {
            return FALSE;
        }

        return (bool) $this->resource;
    }

    /**
     * @{inerhitDoc}
     */
    public function getResource()
    {
        if ($this->resource === NULL) {
            $this->create();
        }

        return $this->resource;
    }

    /**
     * @{inerhitDoc}
     */
    abstract function create();
}