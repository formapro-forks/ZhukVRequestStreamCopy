<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Stream\StreamAbstract;

/**
 * Abstract core tests
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default stream tests
     */
    public function testAbstractStream()
    {
        // Test resolve path
        $this->assertTrue(is_string(StreamAbstract::resolveIncludePath(__FILE__)));
        $this->assertEquals(StreamAbstract::resolveIncludePath(__FILE__), __FILE__);
    }

    /**
     * Test transports
     */
    public function testTransports()
    {
        $this->assertTrue(is_array(StreamAbstract::getTransports()));
        $this->assertEquals(StreamAbstract::getTransports(), stream_get_transports());

        $allowedTransports = StreamAbstract::getTransports();

        foreach ($allowedTransports as $at) {
            $this->assertTrue(StreamAbstract::isTransport($at));
        }
    }

    /**
     * Test wrappers
     */
    public function testWrappers()
    {
        $this->assertTrue(is_array(StreamAbstract::getWrappers()));
        $this->assertEquals(StreamAbstract::getWrappers(), stream_get_wrappers());

        $allowedWrappers = StreamAbstract::getWrappers();

        foreach ($allowedWrappers as $aw) {
            $this->assertTrue(StreamAbstract::isWrapper($aw));
        }
    }
}