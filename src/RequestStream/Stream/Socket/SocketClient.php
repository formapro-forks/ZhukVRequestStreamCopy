<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream\Socket;

use RequestStream\Stream\Exception\SocketErrorException;

/**
 * Client socket connection
 */
class SocketClient extends Socket implements SocketClientInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var integer
     */
    protected $flag = STREAM_CLIENT_CONNECT;

    /**
     * {@inheritDoc}
     */
    public function setFlag($flag)
    {
        $validFlags = array(
            STREAM_CLIENT_CONNECT,
            STREAM_CLIENT_ASYNC_CONNECT,
            STREAM_CLIENT_PERSISTENT,
            (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT),
            (STREAM_CLIENT_CONNECT|STREAM_CLIENT_ASYNC_CONNECT),
        );

        if (!in_array($flag, $validFlags)) {
            throw new \InvalidArgumentException('Undefined flags in own system. Please check flags.');
        }

        $this->flag = $flag;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFlag()
    {
        return $this->flag;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        if ($this->is(false)) {
            return $this->resource;
        }

        if ($this->context) {
            // Create socket client with context
            $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flag, $this->getContext(true));
        } else {
            // Create socket client without context
            $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flag);
        }

        if (!$resource) {
            if (!$errorCode && !$errorStr) {
                throw new SocketErrorException('Socket client not created. Technical error in system.', 0);
            } else {
                throw new SocketErrorException($errorCode . ': ' . $errorStr, $errorCode);
            }
        }

        return $this->resource = $resource;
    }
}