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

use RequestStream\Stream\ContextInterface,
    RequestStream\Stream\StreamAbstract;

/**
 * Abstract core for control stream socket
 */
abstract class Socket extends StreamAbstract implements SocketInterface
{
    /**
     * @var ContextInterface
     */
    protected $context = NULL;

    /**
     * @var string
     */
    protected $transport = NULL;

    /**
     * @var string
     */
    protected $target = NULL;

    /**
     * @var integer
     */
    protected $port = NULL;

    /**
     * @var resource
     */
    protected $resource;

    /**
     * Set context
     *
     * @param ContextInterface $context
     */
    public function setContext(ContextInterface $context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getContext($originalResource = FALSE)
    {
        if (!$this->context) {
            return FALSE;
        }

        return $originalResource ? ($this->context ? $this->context->getResource() : FALSE) : $this->context;
    }

    /**
     * @{inerhitDoc}
     */
    public function setTransport($transport)
    {
        if ($this->is(FALSE)) {
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
     * @{inerhitDoc}
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * @{inerhitDoc}
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @{inerhitDoc}
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
        if (!is_numeric($port) || strpos($port, '.') !== FALSE) {
            throw new \InvalidArgumentException(sprintf('Port must be integer value, "%s" given.', $port));
        }

        if ($port <= 0) {
            throw new \InvalidArgumentException(sprintf('Port must be large zero, "%d" given.', $port));
        }

        $this->port = $port;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @{inerhitDoc}
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
     * @{inerhitDoc}
     */
    public function is($autoload = FALSE)
    {
        if (!$autoload) {
            return (bool) $this->resource;
        }
        else {
            if (!$this->resource) {
                $this->create();
            }

            return (bool) $this->resource;
        }
    }

    /**
     * @{inerhitDoc}
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
        $this->resource = NULL;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function shutdown($mode = STREAM_SHUT_RDWR)
    {
        if (!$this->resource) {
            throw new \LogicException('Can\'t shutdown socket connection. Connection not created.');
        }

        stream_socket_shutdown($this->resource, $mode);

        return $this;
    }
}