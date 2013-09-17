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

use RequestStream\Stream\StreamAbstract;

/**
 * Abstract core for test socket
 */
abstract class SocketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create new socket connection
     */
    abstract protected function createSocket();

    /**
     * Get allowed socket flags
     */
    abstract protected function getSupportedFlags();

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSocketUriRemoteSocket()
    {
        $socket = $this->createSocket();
        $socket->getRemoteSocket();
    }

    /**
     * @dataProvider socketTransportProvider
     */
    public function testSocketTransport($transport, $exception)
    {
        if ($exception) {
            $this->setExpectedException('InvalidArgumentException');
        }

        $socket = $this->createSocket();
        $socket->setTransport($transport);
        $this->assertEquals($transport, $socket->getTransport());
    }

    /**
     * Test socket transport provider
     */
    public function socketTransportProvider()
    {
        $allTransports = array();

        foreach (StreamAbstract::getTransports() as $transport) {
            $allTransports[] = array($transport, false);
        }

        $allTransports[] = array('foo_test1', true);
        $allTransports[] = array('foo_test2', true);

        return $allTransports;
    }

    /**
     * @dataProvider socketPortProvider
     */
    public function testSocketPort($port, $exception)
    {
        if ($exception) {
            $this->setExpectedException('InvalidArgumentException');
        }

        $socket = $this->createSocket();
        $socket->setPort($port);
        $this->assertEquals($port, $socket->getPort());
    }

    /**
     * Test socket port provider
     */
    public function socketPortProvider()
    {
        return array(
            array(1, false),
            array('1.0', true),
            array('foo', true),
            array(-10, true),
            array(80, false)
        );
    }

    /**
     * @dataProvider socketRemoteUriProvider
     */
    public function testSocketRemoteUri($transport, $port, $target, $exception)
    {
        if ($exception) {
            $this->setExpectedException('Exception');
        }

        $socket = $this->createSocket();
        $socket->setTransport($transport);
        $socket->setPort($port);
        $socket->setTarget($target);

        $this->assertEquals($transport . '://' . $target . ':' . $port, $socket->getRemoteSocket());
    }

    /**
     * Provider for testing remote uri
     */
    public function socketRemoteUriProvider()
    {
        return array(
            array(null, null, null, true),
            array('tcp', null, null, true),
            array(null, 80, null, true),
            array(null, null, 'localhost', true),
            array('tcp', 80, null, true),
            array(null, 80, 'localhost', true),
            array('tcp', 80, 'localhost', false),
            array('tcp', 8080, '127.0.0.1', false)
        );
    }

    /**
     * @dataProvider socketFlagsProvider
     */
    public function testSocketFlags($flag, $exception)
    {
        if ($exception) {
            $this->setExpectedException('Exception');
        }

        $socket = $this->createSocket();
        $socket->setFlag($flag);
        $this->assertEquals($flag, $socket->getFlag());
    }

    /**
     * Provider for testing flags
     */
    public function socketFlagsProvider()
    {
        $flags = array();

        foreach ($this->getSupportedFlags() as $flag) {
            $flags[] = array($flag, false);
        }

        $flags[] = array('foo', true);
        $flags[] = array('bar', true);

        return $flags;
    }

    /**
     * @expectedException Exception
     */
    public function testSocketOptionBlocked()
    {
        $socket = $this->createSocket();
        $socket->setBlocking(0);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSocketOptionTimeout()
    {
        $socket = $this->createSocket();
        $socket->setTimeout(0);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSocketOptionSelectRead()
    {
        $socket = $this->createSocket();
        $socket->selectRead();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSocketOptionSelectWrite()
    {
        $socket = $this->createSocket();
        $socket->selectWrite();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSocketOptionSelectExcept()
    {
        $socket = $this->createSocket();
        $socket->selectExcept();
    }
}