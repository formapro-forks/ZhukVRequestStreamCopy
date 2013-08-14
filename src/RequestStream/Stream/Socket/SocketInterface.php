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
use RequestStream\Stream\StreamInterface;

/**
 * Interface for control socket client
 */
interface SocketInterface extends StreamInterface
{
    /**
     * Add context to socket
     *
     * @param ContextInterface $context
     */
    public function setContext(ContextInterface $context);

    /**
     * Get context
     *
     * @param resource $originalResource
     * @return ContextInterface
     */
    public function getContext($originalResource);

    /**
     * Set transport for socket
     *
     * @param string $transport
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
     * @param string $target
     */
    public function setTarget($target);

    /**
     * Get target uri
     *
     * @return string
     */
    public function getTarget();

    /**
     * Set port for open socket
     *
     * @param int $port
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
     * @param int $flag
     */
    public function setFlag($flag);

    /**
     * Get flags for open socket
     *
     * @return int
     */
    public function getFlag();

    /**
     * Get full uri for open socket
     *
     * @return string
     */
    public function getRemoteSocket();

    /**
     * Close socket connection
     */
    public function close();

    /**
     * Shutdown socket connection
     *
     * @see: http://php.net/manual/en/function.stream-socket-shutdown.php
     *
     * @param integer $mode
     */
    public function shutdown($mode = STREAM_SHUT_RDWR);

    /**
     * Blocking stream
     *
     * @param int $mode
     *
     * @return bool
     */
    public function setBlocking($mode);

    /**
     * Set timeout for stream
     *
     * @param int $second
     * @param int $milisecond
     *
     * @return bool
     */
    public function setTimeout($second, $milisecond = 0);

    /**
     * Select read stream
     *
     * @param integer $second
     * @param integer $usecond
     */
    public function selectRead($second = 1, $usecond = 0);

    /**
     * Select write stream
     *
     * @param integer $second
     * @param integer $usecond
     */
    public function selectWrite($second = 1, $usecond = 0);

    /**
     * Select except stream
     *
     * @param integer $second
     * @param integer $usecond
     */
    public function selectExcept($second = 1, $usecond = 0);

    /**
     * Write to socket
     *
     * @param string $content
     * @param int $length
     */
    public function write($content, $length = null);

    /**
     * Read from socket
     *
     * @param int $maxLength
     * @param int $offset
     */
    public function read($maxLength = -1, $offset = -1);
}