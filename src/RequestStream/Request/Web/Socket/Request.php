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
    RequestStreamRequest\Exception\UriException;

/**
 * Core for socket web request
 */
class Request extends WebAbstract implements RequestInterface
{
    // Socket
    protected $socket = NULL;

    // Bind to
    protected $bindto = NULL;

    /**
     * Construct
     */
    public function __construct($uri = NULL)
    {
        parent::__construct($uri);
    }

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
    public function setBindTo($bindto)
    {
        $this->bindto = $bindto;
    }

    /**
     * Init socket
     */
    protected function initSocket()
    {
        // Set transport and port for socket request
        return $this->socket->setTransport('tcp')
            ->setPort(is_null($this->uri->getPort()) ? 80 : $this->uri->getPort());
    }

    /**
     * Init socket of SSL
     */
    protected function initSocketSSL()
    {
        // Set transport and port for socket request
        return $this->socket->setTransport('ssl')
            ->setPort(is_null($this->uri->getPort()) ? 443 : $this->uri->getPort());
    }

    /**
     * Send request
     */
    protected function createRequest()
    {
        $this->socket = new Socket;

        // Init socket by scheme
        if ($this->uri->getSecure()) {
            $this->initSocketSSL();
        }
        else {
            $this->initSocket();
        }

        // Create context and set context in socket
        $context = new Context;
        if ($this->bindto) {
            $context->setOptions('socket', 'bindto', $this->bindto);
        }

        // If using proxy
        if ($this->proxy) {
            //$this->socket->setTarget($this->proxy['host']);
            //$this->socket->setPort($this->proxy['port']);
            //// Now only tcp transport allowed
            //$this->socket->setTransport('tcp');
        }
        else {
            $this->socket->setTarget($this->uri->getHost());
        }

        $this->socket->setContext($context);

        // Create socket connect
        $this->socket->create();

        // Validate post data and xml data
        $this->validateXmlPostData();

        // Write headers to socket
        $this->writeHeaderToSocket();

        // Generate result
        $this->generateResult($this->socket->readAllFromSocket());
    }

    /**
     * Prepare headers
     *  Set POST method if send other variables
     */
    protected function prepareHeaders()
    {
        parent::prepareHeaders();

        if ($this->post_data) {
            // Add content type for request
            $this->addHeader('Content-Type', 'multipart/form-data; boundary=' . $this->getPostBoundary());

            // If added content length header, must be deleted!
            $this->deleteHeader('Content-Length');
        }
    }

    /**
     * Write to socket all headers and self info request
     */
    protected function writeHeaderToSocket()
    {
        // Prepare headers
        $this->prepareHeaders();

        // Write method data transfer, uri, http version and host headers
        $write = $this->getMethod() . ' ' . ((string) $this->uri) . ' HTTP/' . $this->getHttpVersion() .  "\r\n";
        $write .= 'Host: ' . $this->uri->getHost() . "\r\n";
        $this->socket->writeToSocket($write);

        // Write other headers
        foreach ($this->getHeaders() as $headerName => $headerValue) {
          $this->socket->writeToSocket($headerName . ': ' . $headerValue . "\r\n");
        }

        // Send cookie
        if ($cookieStr = $this->getHeaderCookiesString()) {
          $this->socket->writeToSocket('Cookie: ' . $cookieStr . "\r\n");
        }

        // Send post data
        if ($this->post_data && $this->getMethod() == 'POST') {
            $postData = $this->writeHeaderToSocketPostData();
            // Get length post data
            $postDataSize = mb_strlen($postData, mb_detect_encoding($postData));
            // Write content length
            // @see: http://en.wikipedia.org/wiki/HTTP
            $this->socket->writeToSocket('Content-Length: ' . $postDataSize . "\n");
            // Write all post data
            $this->socket->writeToSocket($postData);
        }

        // Send XML data
        if ($this->xml_data && $this->getMethod() == 'POST') {
            $xmlStr = "\r\n" . $this->getXmlDataString();
            // Write content length
            $this->socket->writeToSocket('Content-Length: ' . mb_strlen($xmlStr));

            $this->socket->writeToSocket("\r\n" . $xmlStr);
        }

        // End line write to socket
        $this->socket->writeToSocket("\r\n\r\n");
    }

    /**
     * Generate header for post data
     */
    protected function writeHeaderToSocketPostData()
    {
        $postData = "\r\n";

        foreach ($this->post_data as $fName => $fValue) {
            $postData .= '--' . $this->getPostBoundary(FALSE) . "\r\n" .
                'Content-Disposition: form-data; name="' . $fName . '"' . "\r\n\r\n" .
                $fValue . "\n";
        }

        $postData .= '--' . $this->getPostBoundary() . '--';

        return $postData;
    }
}