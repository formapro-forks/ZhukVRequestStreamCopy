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

use RequestStream\Stream\Socket\Formatter\OutputFormatterInterface;

interface ConnectionInterface
{
    const OUTPUT_NORMAL = 1;
    const OUTPUT_RAW = 2;
    const OUTPUT_PLAIN = 3;

    /**
     * Close connection
     */
    public function close();

    /**
     * Is connection exists
     */
    public function is();

    /**
     * Set output formatter
     *
     * @param OutputFormatterInterface $outputFormatter
     */
    public function setOutputFormatter(OutputFormatterInterface $outputFormatter);

    /**
     * Get output formatter
     *
     * @return OutputFormatterInterface
     */
    public function getOutputFormatter();

    /**
     * Write to connection
     *
     * @param string $message
     * @param bool $newLine
     * @param int $type
     */
    public function write($message, $newLine = false, $type = self::OUTPUT_NORMAL);

    /**
     * Write line to connection
     *
     * @param string $text
     * @param int $type
     */
    public function writeln($message, $type = self::OUTPUT_NORMAL);
}