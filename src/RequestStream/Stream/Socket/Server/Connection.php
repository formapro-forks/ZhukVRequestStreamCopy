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

use RequestStream\Stream\Socket\Formatter\OutputFormatter;
use RequestStream\Stream\Socket\Formatter\OutputFormatterInterface;

class Connection implements ConnectionInterface
{
    /**
     * @var resource
     */
    private $connection;

    /**
     * @var OutputFormatterInterface
     */
    private $outputFormatter;

    /**
     * Construct
     *
     * @param resource $connection
     * @param OutputFormatterInterface $formatter
     */
    public function __construct($connection, OutputFormatterInterface $formatter = null)
    {
        $this->connection = $connection;
        $this->outputFormatter = $formatter ?: new OutputFormatter(true);
    }


    /**
     * {@inheritDoc}
     */
    public function is()
    {
        return (bool) $this->connection;
    }

    /**
     * {@inheritDoc}
     */
    public function close()
    {
        fclose($this->connection);
        unset ($this->connection);
    }

    /**
     * {@inheritDoc}
     */
    public function setOutputFormatter(OutputFormatterInterface $outputFormatter)
    {
        $this->outputFormatter = $outputFormatter;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getOutputFormatter()
    {
        return $this->outputFormatter;
    }

    /**
     * {@inheritDoc}
     */
    public function write($messages, $newLine = false, $type = self::OUTPUT_NORMAL)
    {
        $messages = (array) $messages;

        foreach ($messages as $message) {
            switch ($type) {
                case self::OUTPUT_NORMAL:
                    $message = $this->outputFormatter->format($message);
                    break;

                case self::OUTPUT_RAW:
                    break;

                case self::OUTPUT_PLAIN:
                    $message = strip_tags($this->outputFormatter->format($message));
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Unknown output type given (%s)', $type));
            }

            fwrite($this->connection, $message . ($newLine ? PHP_EOL : ''));
        }
    }

    /**
     * {@inheritDoc}
     */
    public function writeln($message, $type = self::OUTPUT_NORMAL)
    {
        $this->write($message, true, $type);
    }
}