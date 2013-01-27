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

/**
 * Interface for control socket client
 */
interface SocketClientInterface
{
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
     * Get all contents from stream
     * @see http://www.php.net/manual/en/function.stream-get-contents.php
     *
     * @param int $maxLength
     * @param int $offset
     *
     * @return string
     */
    public function getContents($maxLength = -1, $offset = -1);

}