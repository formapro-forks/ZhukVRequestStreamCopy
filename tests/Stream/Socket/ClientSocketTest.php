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

/**
 * Client socket test
 */
class ClientSocketTest extends SocketTest
{
    /**
     * {@inheritDoc}
     */
    protected function createSocket()
    {
        return new SocketClient();
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedFlags()
    {
        return array(
            STREAM_CLIENT_CONNECT,
            STREAM_CLIENT_ASYNC_CONNECT,
            STREAM_CLIENT_PERSISTENT,
            (STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT),
            (STREAM_CLIENT_CONNECT|STREAM_CLIENT_ASYNC_CONNECT),
        );
    }

    /**
     * Create socket to google connection
     *
     * @return SocketClient
     */
    protected function createSocketGoogleConnection()
    {
        $socket = new SocketClient();

        return $socket
            ->setTransport('tcp')
            ->setPort(80)
            ->setTarget('google.com');
    }

    /**
     * Base write header to google connection
     */
    protected function baseWriteHeaderToGoogleConnection(SocketClient $socket = null)
    {
        $message = "GET / HTTP/1.0\nHost: google.com\n\n";

        if ($socket) {
            $socket->write($message);
        }

        return $message;
    }

    /**
     * Test client socket
     */
    public function testSocketClient()
    {
        $socketClient = new SocketClient();

        $this->assertInstanceOf('RequestStream\Stream\Socket\SocketInterface', $socketClient);

        /** @var SocketClient $socketClient */
        $socketClient = $this->createSocketGoogleConnection();

        // Create socket to google server
        $socketClient->create();
        $this->assertTrue(is_resource($socketClient->getResource()));

        $writeText = $this->baseWriteHeaderToGoogleConnection();

        // Write request
        $writeLength = $socketClient->write($writeText);

        $this->assertEquals(strlen($writeText), $writeLength);

        // Test read
        $this->assertNotNull($socketClient->read());
    }

    /**
     * Test socket options
     */
    public function testSocketOptions()
    {
        $socket = $this->createSocketGoogleConnection();

        $socket->create();

        $this->assertFalse($socket->selectRead());
        $this->assertTrue($socket->selectWrite());
        $this->assertFalse($socket->selectExcept());

        $write = trim($this->baseWriteHeaderToGoogleConnection()) . "\nConnection: close\n\n";

        $socket->write($write);

        $this->assertTrue($socket->selectRead());

        $socket->shutdown(STREAM_SHUT_WR);

        $this->assertEquals(0, @$socket->write('foo'));

        $socket->close();

        $refProperty = new \ReflectionProperty($socket, 'resource');
        $refProperty->setAccessible(true);
        $this->assertFalse(is_resource($refProperty->getValue($socket)));
    }
}
