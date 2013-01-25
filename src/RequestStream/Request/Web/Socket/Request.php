<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */


namespace RequestStream\Request\Web\Socket;

use RequestStream\Request\Web\WebAbstract,
    RequestStream\Stream\Socket\Socket,
    RequestStream\Stream\Context\Context,
    RequestStream\Request\Web\Result;

/**
 * Core for socket web request
 */
class Request extends WebAbstract implements RequestInterface
{
    /**
     * @var resource
     */
    protected $socket = NULL;

    /**
     * @var string
     */
    protected $bindTo = NULL;

    /**
     * Set bind to
     *    Set other IP and port for request in our system
     *
     * @param string $ip
     *    Ip and port for connext
     *    Must be mask: ip:port
     *    Can use IPv4 and IPv6
     *
     * @see http://www.php.net/manual/en/context.socket.php
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
        $this->socket = new Socket;
        $requestUri = $this->request->getUri();

        // Init socket by scheme
        if ($requestUri->getSecure()) {
            $this->initSocketSSL();
        }
        else {
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
        }
        else {
            $this->socket->setTarget($requestUri->getHost());
        }

        $this->socket->setContext($context);

        // Create socket connect
        $this->socket->create();

        // Write headers to socket
        $this->writeHeaderToSocket();

        // Start usage time
        $useTime = microtime(TRUE);

        // Generate result
        return Result::parseFromContent($this->socket->readAll(), microtime(TRUE) - $useTime);
    }

    /**
     * @{inerhitDoc}
     */
    protected function prepareHeaders()
    {
        parent::prepareHeaders();

        if (count($this->postData)) {
            // If added content length header, must be deleted!
            unset ($this->headers['Content-Length']);
        }
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