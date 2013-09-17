<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web\Socket;

use RequestStream\Request\Web\WebAbstract;
use RequestStream\Stream\Socket\SocketClient;
use RequestStream\Stream\Context;
use RequestStream\Request\Web\Result;

/**
 * Core for socket web connection
 */
class Connection extends WebAbstract implements ConnectionInterface
{
    /**
     * @var SocketClient
     */
    protected $socket;

    /**
     * @var string
     */
    protected $bindTo;

    /**
     * {@inheritDoc}
     */
    public function setBindTo($bindTo)
    {
        $this->bindTo = $bindTo;
    }

    /**
     * Init socket
     */
    protected function initSocket()
    {
        // Set transport and port for socket request
        return $this->socket->setTransport('tcp')
            ->setPort(is_null($this->request->getUri()->getPort()) ? 80 : $this->request->getUri()->getPort());
    }

    /**
     * Init socket of SSL
     */
    protected function initSocketSSL()
    {
        // Set transport and port for socket request
        return $this->socket->setTransport('ssl')
            ->setPort(is_null($this->request->getUri()->getPort()) ? 443 : $this->request->getUri()->getPort());
    }

    /**
     * Create request
     */
    protected function createRequest()
    {
        $this->socket = new SocketClient;
        $requestUri = $this->request->getUri();

        // Init socket by scheme
        if ($requestUri->getSecure()) {
            $this->initSocketSSL();
        } else {
            $this->initSocket();
        }

        // Create context and set context in socket
        $context = new Context;

        if ($this->bindTo) {
            $context->setOptions('socket', 'bindto', $this->bindTo);
        }

        // If using proxy
        if ($this->request->getProxy()) {
            $this->socket->setTarget($this->request->getProxy()->getHost());
            $this->socket->setPort($this->request->getProxy()->getPort());
            // Now only tcp transport allowed
            $this->socket->setTransport('tcp');
        } else {
            $this->socket->setTarget($requestUri->getHost());
        }

        $this->socket->setContext($context);

        // Create socket connect
        $this->socket->create();

        // Write headers to socket
        $this->writeHeaderToSocket();

        // Start usage time
        $useTime = microtime(true);

        // Generate result
        return Result::parseFromContent($this->request, $this->socket->read(), microtime(true) - $useTime);
    }

    /**
     * Write to socket all headers and self info request
     */
    protected function writeHeaderToSocket()
    {
        // Write headers
        $this->socket->write((string) $this->request);
    }
}