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

use RequestStream\Stream\ContextInterface;
use RequestStream\Stream\StreamAbstract;

/**
 * Abstract core for control stream socket
 */
abstract class Socket extends StreamAbstract implements SocketInterface
{
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @var string
     */
    protected $transport;

    /**
     * @var string
     */
    protected $target;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * {@inheritDoc}
     */
    public function setContext(ContextInterface $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getContext($originalResource = false)
    {
        if (!$this->context) {
            return false;
        }

        return $originalResource ? ($this->context ? $this->context->getResource() : false) : $this->context;
    }

    /**
     * {@inheritDoc}
     */
    public function setTransport($transport)
    {
        if ($this->is(false)) {
            throw new \RuntimeException('Can\'t set transport for socket. Socket is started.');
        }

        // Validate transport
        if (!self::isTransport($transport)) {
            throw new \InvalidArgumentException(sprintf('Undefined transport "%s". Allowed transports: "%s"', $transport, implode('", "', self::getTransports())));
        }

        $this->transport = $transport;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * {@inheritDoc}
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set port
     *
     * @param int $port
     */
    public function setPort($port)
    {
        // Validate port
        if (!is_numeric($port) || strpos($port, '.') !== false) {
            throw new \InvalidArgumentException(sprintf('Port must be integer value, "%s" given.', $port));
        }

        if ($port <= 0) {
            throw new \InvalidArgumentException(sprintf('Port must be large zero, "%d" given.', $port));
        }

        $this->port = $port;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritDoc}
     */
    public function getRemoteSocket()
    {
        if (!$this->transport) {
            throw new \InvalidArgumentException('Can\'t get remote socket. Undefined transport.');
        }

        if (!$this->target) {
            throw new \InvalidArgumentException('Can\'t get remote socket. Undefined target.');
        }

        if (!$this->port) {
            throw new \InvalidArgumentException('Can\'t get remote socket. Undefined port.');
        }

        return $this->transport . '://' . $this->target . ($this->port ? ':' . $this->port : '');
    }

    /**
     * {@inheritDoc}
     */
    public function is($autoload = false)
    {
        if (!$autoload) {
            return (bool) $this->resource;
        } else {
            if (!$this->resource) {
                $this->create();
            }

            return (bool) $this->resource;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        if (!$this->resource) {
            throw new \LogicException('Can\'t close socket connection. Connection not created.');
        }

        // Shutdown socket connection
        $this->shutdown();

        // Destruct
        unset ($this->resource);
        $this->resource = null;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function shutdown($mode = STREAM_SHUT_RDWR)
    {
        if (!$this->resource) {
            throw new \LogicException('Can\'t shutdown socket connection. Connection not created.');
        }

        stream_socket_shutdown($this->resource, $mode);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setBlocking($mode)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t set blocking of mode stream. Socket not created.');
        }

        return stream_set_blocking($this->getResource(), $mode);
    }

    /**
     * {@inheritDoc}
     */
    public function setTimeout($second, $milisecond = 0)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t set timetout. Socket not created.');
        }

        return stream_set_timeout($this->getResource(), $second, $milisecond);
    }

    /**
     * {@inheritDoc}
     */
    public function selectRead($seconds = 1, $useconds = 0)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t get read select. Socket not created.');
        }

        $selectRead = array($this->resource);
        $null = null;
        return (bool) self::select($selectRead, $null, $null, $seconds, $useconds);
    }

    /**
     * {@inheritDoc}
     */
    public function selectWrite($second = 1, $usecond = 0)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t get write select. Socket not created.');
        }

        $selectWrite = array($this->resource);
        $null = null;
        return (bool) self::select($null, $selectWrite, $null, $second, $usecond);
    }

    /**
     * {@inheritDoc}
     */
    public function selectExcept($second = 1, $usecond = 0)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t get except select. Socket not created.');
        }

        $selectExcept = array($this->resource);
        $null = null;
        return (bool) self::select($null, $null, $selectExcept, $second, $usecond);
    }

    /**
     * {@inheritDoc}
     */
    public function write($content, $length = null)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t write to socket. Socket not created.');
        }

        if ($length === null) {
            return fwrite($this->getResource(), $content);
        } else {
            return fwrite($this->getResource(), $content, $length);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function read($length = -1, $offset = -1)
    {
        if (!$this->is(false)) {
            throw new \RuntimeException('Can\'t read from socket. Socket not started.');
        }

        return stream_get_contents($this->resource, $length, $offset);
    }
}