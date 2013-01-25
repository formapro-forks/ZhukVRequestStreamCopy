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

use RequestStream\Stream\Context\ContextInterface;

/**
 * Interface for control socket client
 */
interface SocketInterface
{
    /**
     * Add context to socket
     *
     * @param ContextInterface $context
     *
     * @return this object
     */
    public function setContext(ContextInterface $context);

    /**
     * Get context
     *
     * @return ContextInterface
     */
    public function getContext($originalResource);

    /**
     * Set transport for socket
     *
     * @param string $transport
     *
     * @return this object
     *    Exception, if transport not allowed in system
     */
    public function setTransport($transport);

    /**
     * Get transport
     *
     * @return string
     */
    public function getTransport();

    /**
     * Set target
     *
     * @param string $target      Target uri for open socket
     *
     * @return this object
     */
    public function setTarget($target);

    /**
     * Get target uri
     *
     * @return string
     */
    public function getTarget();

    /**
     * Set posrt for open socket
     *
     * @param int $port
     *
     * @return this object
     *    Exception, if port not valid
     */
    public function setPort($port);

    /**
     * Get port
     *
     * @return int
     */
    public function getPort();

    /**
     * Set flag for open socket
     *
     * @param int $flags
     *
     * @return this object
     *    Exception, if flag not allowed
     */
    public function setFlags($flags);

    /**
     * Get flags for open socket
     *
     * @return int
     */
    public function getFlags();

    /**
     * Get full uri for open socket
     *
     * @return string
     *    Exception, if port or target or transport is undefined
     */
    public function getRemoteSocket();

    /**
     * Create socket connect
     *
     * @return null
     *    Exception, if socket not opened or is error
     */
    public function create();

    /**
     * Write to socket
     *
     * @param string $content
     * @param int $length
     */
    public function write($content, $length = NULL);

    /**
     * Read from socket
     *
     * @param int $length
     */
    public function read($length);

    /**
     * Is eof of reading socket
     *
     * @return bool
     */
    public function isEof();

    /**
     * Read all content from socket
     *
     * @return string
     */
    public function readAll();
}