<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream\Socket;

/**
 * Client socket test
 */
class ClientSocketTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create socket to google connection
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
    protected function baseWriteHeaderToGoogleConnection(SocketClient $socket = NULL)
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

        $this->assertTrue($socketClient instanceof SocketInterface);

        $socketClient = $this->createSocketGoogleConnection();

        // Create socket to google server
        $socketClient->create();
        $this->assertTrue(is_resource($socketClient->getResource()));

        $writeText = $this->baseWriteHeaderToGoogleConnection();
        // Write request
        $writeLength = $socketClient->write($writeText);

        $this->assertEquals($writeLength, strlen($writeText));

        // Test read
        $this->assertNotNull($socketClient->read());
    }

    /**
     * Test socket remote URI
     */
    public function testSocketRemoteUri()
    {
        $socketClient = new SocketClient;

        try {
            $socketClient->getRemoteSocket();
            $this->fail('Can\'t create remote socket wihout port, transport and target.');
        }
        catch (\Exception $e){
        }

        $socketClient->setTransport('tcp');

        try {
            $socketClient->getRemoteSocket();
            $this->fail('Can\'t create remote socket without port and target');
        }
        catch (\Exception $e){
        }

        $socketClient->setPort(80);

        try {
            $socketClient->getRemoteSocket();
            $this->fail('Can\'t create remote socket without target.');
        }
        catch (\Exception $e){
        }

        $socketClient->setTarget('google.com');

        $this->assertEquals($socketClient->getRemoteSocket(), 'tcp://google.com:80');

        try {
            $socketClient->setPort('foo');
            $this->fail('Not control port.');
        }
        catch (\InvalidArgumentException $e){
        }

        try {
            $socketClient->setTransport('foo');
            $this->fail('Not control transport.');
        }
        catch (\InvalidArgumentException $e){
        }
    }

    /**
     * Test flags
     */
    public function testSocketFlags()
    {
        $allowedFlags = array(STREAM_CLIENT_CONNECT, STREAM_CLIENT_ASYNC_CONNECT, STREAM_CLIENT_PERSISTENT);

        $socket = new SocketClient;
        foreach ($allowedFlags as $flag) {
            $socket->setFlag($flag);
        }

        try {
            $socket->setFlag('Undefined');
            $this->fail('Not control flag.');
        }
        catch (\Exception $e){
        }
    }

    /**
     * Test socket options
     */
    public function testSocketOptions()
    {
        $socket = $this->createSocketGoogleConnection();

        try {
            $socket->setBlocking(0);
            $this->fail('Can\'t set blocking mode.');
        }
        catch (\Exception $e){
        }

        try {
            $socket->setTimeout(0);
            $this->fail('Can\'t set timeout.');
        }
        catch (\Exception $e){
        }

        try {
            $socket->selectRead();
            $this->fail('Can\'t get select read.');
        }
        catch (\Exception $e){
        }

        try {
            $socket->selectWrite();
            $this->fail('Can\'t get select write.');
        }
        catch (\Exception $e){
        }

        try {
            $socket->selectExcept();
            $this->fail('Can\'t get select except.');
        }
        catch (\Exception $e){
        }

        $socket->create();

        $this->assertFalse($socket->selectRead());
        $this->assertTrue($socket->selectWrite());
        $this->assertFalse($socket->selectExcept());

        $write = trim($this->baseWriteHeaderToGoogleConnection()) . "\nConnection: close\n\n";

        $socket->write($write);

        $this->assertTrue($socket->selectRead());

        $socket->shutdown(STREAM_SHUT_WR);

        $this->assertEquals(@$socket->write('foo'), 0);

        $socket->close();

        $refProperty = new \ReflectionProperty($socket, 'resource');
        $refProperty->setAccessible(TRUE);
        $this->assertFalse(is_resource($refProperty->getValue($socket)));
    }
}
