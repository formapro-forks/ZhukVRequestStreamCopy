<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream;

/**
 * Abstract core tests
 */
class StreamAbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Default stream tests
     */
    public function testAbstractStream()
    {
        // Test resolve path
        $this->assertTrue(is_string(StreamAbstract::resolveIncludePath(__FILE__)));
        $this->assertEquals(__FILE__, StreamAbstract::resolveIncludePath(__FILE__));
    }

    /**
     * Test get transports
     */
    public function testGetTransports()
    {
        $this->assertEquals(stream_get_transports(), StreamAbstract::getTransports());
    }

    /**
     * @dataProvider providerTransports
     */
    public function testTransports($transport, $exists)
    {
        if (true === $exists) {
            $this->assertTrue(StreamAbstract::isTransport($transport));
        } else {
            $this->assertFalse(StreamAbstract::isTransport($transport));
        }
    }

    /**
     * Provider for test transports
     */
    public function providerTransports()
    {
        $transports = array();

        foreach (StreamAbstract::getTransports() as $transport) {
            $transports[] = array($transport, true);
        }

        $transports[] = array('undefined_transport', false);

        return $transports;
    }

    /**
     * Test get wrappers
     */
    public function testGetWrappers()
    {
        $this->assertEquals(stream_get_wrappers(), StreamAbstract::getWrappers());
    }

    /**
     * @dataProvider providerWrappers
     */
    public function testWrappers($wrapper, $exists)
    {
        if (true === $exists) {
            $this->assertTrue(StreamAbstract::isWrapper($wrapper));
        } else {
            $this->assertFalse(StreamAbstract::isWrapper($wrapper));
        }
    }

    /**
     * Provider for testing wrappers
     */
    public function providerWrappers()
    {
        $wrappers = array();

        foreach (StreamAbstract::getWrappers() as $wp) {
            $wrappers[] = array($wp, true);
        }

        $wrappers[] = array('undefined_wrapper', false);

        return $wrappers;
    }
}