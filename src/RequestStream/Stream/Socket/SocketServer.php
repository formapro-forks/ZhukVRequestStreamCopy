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
use RequestStream\Stream\Socket\Server\AcceptCommandInterface;
use RequestStream\Stream\Socket\Server\Connection;

/**
 * Server socket
 */
class SocketServer extends Socket implements SocketServerInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var integer
     */
    protected $flag;

    /**
     * Use only PHP >= 5.5
     *
     * @var string
     */
    protected $processTitle;

    /**
     * @var AcceptCommandInterface
     */
    protected $acceptCommand;

    /**
     * @var int
     */
    protected $acceptTimeout;

    /**
     * Construct
     */
    public function __construct()
    {
        // Set default flag
        $this->flag = STREAM_SERVER_BIND | STREAM_SERVER_LISTEN;
    }

    /**
     * {@inheritDoc}
     */
    public function setProcessTitle($title)
    {
        // Only PHP >= 5.5 supported
        if (version_compare(PHP_VERSION, '5.5') < 0) {
            throw new \BadMethodCallException(sprintf(
                'Can\'t set process title. Unsupported version PHP: "%s". Supported only PHP >= 5.5',
                PHP_VERSION
            ));
        }

        if (PHP_SAPI === 'cli') {
            $this->processTitle = $title;
            if ($this->resource) {
                cli_set_process_title($title);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setFlag($flag)
    {
        $validFlags = array(
            STREAM_SERVER_BIND,
            STREAM_SERVER_LISTEN
        );

        if (!in_array($flag, $validFlags)) {
            throw new \InvalidArgumentException('Undefined flags in own system. Please check flags.');
        }

        $this->flag = $flag;
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
    public function setAcceptTimeout($seconds)
    {
        $this->acceptTimeout = (int) $seconds;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAcceptTimeout()
    {
        return $this->acceptTimeout;
    }

    /**
     * Create stream
     */
    public function create()
    {
        if ($this->is(false)) {
            return $this->resource;
        }

        if ($this->context) {
            // Create socket server with context
            $resource = stream_socket_server($this->getRemoteSocket(), $errorCode, $errorStr, $this->flag, $this->getContext(true));
        } else {
            // Create socket server without context
            $resource = stream_socket_server($this->getRemoteSocket(), $errorCode, $errorStr, $this->flag);
        }

        if (!$resource) {
            if (!$errorCode && !$errorStr) {
                throw new SocketErrorException('Socket server not created. Technical error in system.', 0);
            } else {
                throw new SocketErrorException($errorCode . ': ' . $errorStr, $errorCode);
            }
        }

        if ($this->processTitle) {
            cli_set_process_title($this->processTitle);
        }

        return $this->resource = $resource;
    }

    /**
     * {@inheritDoc}
     */
    public function setAcceptCommand(AcceptCommandInterface $command = null)
    {
        $this->acceptCommand = $command;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function acceptRun()
    {
        if (!$this->acceptCommand) {
            throw new \RuntimeException('Can\'t accept run. Undefined accept command.');
        }

        while ($connection = stream_socket_accept($this->resource, null !== $this->acceptTimeout ? $this->acceptTimeout : ini_get('default_socket_timeout'))) {
            $connection = new Connection($connection);
            $this->acceptCommand->run($connection);

            if ($this->acceptCommand->isAutoClose() && $connection->is()) {
                // Auto close connection
                $connection->close();
            }
        }
    }
}