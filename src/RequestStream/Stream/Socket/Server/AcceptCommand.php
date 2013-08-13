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

use RequestStream\Stream\Socket\Helper\FormatterHelper;
use RequestStream\Stream\Socket\Helper\HelperSet;

/**
 * Abstract accept command
 */
class AcceptCommand implements AcceptCommandInterface
{
    /**
     * @var HelperSet
     */
    private $helperSet;

    /**
     * @var bool
     */
    private $isInit = false;

    /**
     * Construct
     */
    public function __construct()
    {
        // Set default helper set
        $this->helperSet = new HelperSet(array(
            new FormatterHelper()
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function isAutoClose()
    {
        return true;
    }

    /**
     * Execute command
     */
    protected function execute(ConnectionInterface $connection)
    {
        throw new \LogicException('You must override the execute() method in the concrete command class.');
    }

    /**
     * Initialize command
     */
    protected function initialize()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function run(ConnectionInterface $connection)
    {
        if (!$this->isInit) {
            $this->initialize();
            $this->isInit = true;
        }

        $this->execute($connection);
    }

    /**
     * {@inheritDoc}
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * {@inheritDoc}
     */
    public function getHelper($name)
    {
        return $this->helperSet->get($name);
    }
}