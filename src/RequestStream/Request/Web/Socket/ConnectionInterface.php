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

use RequestStream\Request\Web\WebInterface;

/**
 * Interface for control socket connection
 */
interface ConnectionInterface extends WebInterface
{
    /**
     * Set bind to
     *    Set other IP and port for request in our system
     *
     * @param string $bindto
     * @see http://www.php.net/manual/en/context.socket.php
     */
    public function setBindTo($bindto);
}