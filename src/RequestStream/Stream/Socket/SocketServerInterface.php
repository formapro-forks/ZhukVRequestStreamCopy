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

use RequestStream\Stream\Socket\Server\AcceptCommandInterface;

/**
 * Interface for control socket client
 */
interface SocketServerInterface extends SocketInterface
{
    /**
     * Set process title
     * Attention: Only PHP > 5.5 supported
     *
     * @param string $title
     */
    public function setProcessTitle($title);

    /**
     * Set accept command
     * This command will called after create new connection
     *
     * @param AcceptCommandInterface $acceptCommand
     */
    public function setAcceptCommand(AcceptCommandInterface $acceptCommand);

    /**
     * Accept run. Start loop and check open connection
     */
    public function acceptRun();

    /**
     * Set accept timeout (in seconds)
     *
     * @param int $seconds
     */
    public function setAcceptTimeout($seconds);

    /**
     * Get accept timeout
     *
     * @return int
     */
    public function getAcceptTimeout();
}