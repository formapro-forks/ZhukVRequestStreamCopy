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

/**
 * Interface for control socket request
 */
interface RequestInterface
{
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
    public function setBindTo($bindto);
}