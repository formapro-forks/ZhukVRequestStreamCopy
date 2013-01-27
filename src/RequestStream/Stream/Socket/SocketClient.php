<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream\Socket;

use RequestStream\Stream\Exception\SocketErrorException;

/**
 * Client socket connection
 */
class SocketClient extends Socket
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var integer
     */
    protected $flags = STREAM_CLIENT_CONNECT;

    /**
     * @{inerhitDoc}
     */
    public function setFlags($flags)
    {
        if (!in_array($flags, array(STREAM_CLIENT_CONNECT, STREAM_CLIENT_ASYNC_CONNECT, STREAM_CLIENT_PERSISTENT))) {
            throw new \InvalidArgumentException('Undefined flags in own system. Please check flags.');
        }

        $this->flags = $flags;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getFlags()
    {
        return $this->flags;
    }

        /**
     * @{inerhitDoc}
     */
    public function create()
    {
        if ($this->is(FALSE)) {
            return $this->resource;
        }

        if ($this->context) {
            $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flags, $this->getContext(TRUE));
        }
        else {
            $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flags);
        }

        if (!$resource) {
            if (!$errorCode && !$errorStr) {
                throw new SocketErrorException('Socket not create. Technical error in system.', 0);
            }
            else {
                throw new SocketErrorException($errorCode . ': ' . $errorStr, $errorCode);
            }
        }

        $this->resource = $resource;
    }

    /**
     * @{inerhitDoc}
     */
    public function write($content, $length = NULL)
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t write to socket. Socket not created.');
        }

        if ($length === NULL) {
            return fwrite($this->getResource(), $content);
        }
        else {
            return fwrite($this->getResource(), $content, $length);
        }
    }

    /**
     * @{inerhitDoc}
     */
    public function read($length)
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t read from socket. Socket not started.');
        }

        return fread($this->getResource(), $length);
    }

    /**
     * @{inerhitDoc}
     */
    public function isEof()
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t read from socket. Socket not started');
        }

        return eof($this->getResource());
    }

    /**
     * @{inerhitDoc}
     */
    public function getContents($maxLength = -1, $offset = -1)
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t get content. Stream not created.');
        }

        return stream_get_contents($this->getResource(), $maxLength, $offset);
    }


    /**
     * @{inerhitDoc}
     */
    public function getLine($length, $ending = "\n")
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t get line. Stream not created.');
        }

        return stream_get_line($this->getResource(), $length, $ending);
    }

    /**
     * @{inerhitDoc}
     */
    public function setBlocking($mode)
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t set blocking of mode stream. Stream not created.');
        }

        return stream_set_blocking($this->getResource(), $mode);
    }

    /**
     * @{inerhitDoc}
     */
    public function setTimeout($second, $milisecond = 0)
    {
        if (!$this->is(FALSE)) {
            throw new \RuntimeException('Can\'t set timetout. Stream not created.');
        }

        return stream_set_timeout($this->getResource(), $second, $milisecond);
    }
}