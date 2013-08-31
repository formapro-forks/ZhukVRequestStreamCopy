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
 * Core for socket web request
 *
 * @deprecated Please use Connection instance
 */
class Request extends Connection implements RequestInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct($uri = null)
    {
        trigger_error('Socket\Request class is deprecated and will be removed. Please use Socket\Connection.', E_USER_DEPRECATED);

        parent::__construct($uri);
    }
}