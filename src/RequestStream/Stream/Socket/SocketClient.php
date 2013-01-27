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

        return $this->resource = $resource;
    }
}