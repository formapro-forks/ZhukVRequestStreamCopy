<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream\Socket\Server;

use RequestStream\Stream\Socket\Helper\Helper;
use RequestStream\Stream\Socket\Helper\HelperSet;

interface AcceptCommandInterface
{
    /**
     * Execute command
     *
     * @param ConnectionInterface $connection
     */
    public function run(ConnectionInterface $connection);

    /**
     * Set helper set
     *
     * @param HelperSet $helperSet
     */
    public function setHelperSet(HelperSet $helperSet);

    /**
     * Get helper set
     *
     * @return HelperSet
     */
    public function getHelperSet();

    /**
     * Get helper
     *
     * @param string $name
     * @return \RequestStream\Stream\Socket\Helper\HelperInterface
     */
    public function getHelper($name);

    /**
     * Is auto close
     *
     * @return bool
     */
    public function isAutoClose();
}