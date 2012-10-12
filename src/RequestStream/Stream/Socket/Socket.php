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

use RequestStream\Stream\Context\ContextInterface,
    RequestStream\Stream\StreamAbstract,
    RequestStream\Stream\Exception\SocketErrorException;

/**
 * Abstract core for control stream socket
 */
class Socket extends StreamAbstract implements SocketInterface {
  // Context
  protected $context = NULL;
  
  // Transport
  protected $transport = NULL;
  
  // Target
  protected $target = NULL;
  
  // Port
  protected $port = NULL;
  
  // Flag
  protected $flags = STREAM_CLIENT_CONNECT;

  /**
   * Construct
   */
  public function __construct()
  {
    
  }
  
  /**
   * Set context
   *
   * @param ContextInterface $context
   */
  public function setContext(ContextInterface $context)
  {
    $this->context = $context;
    return $this;
  }
  
  /**
   * Get context
   *
   * @param bool [$originalResource]
   *
   * @return resource or ContextInterface
   */
  public function getContext($originalResource = FALSE)
  {
    if (!$this->context) { return FALSE; }
    return $originalResource ? ($this->context ? $this->context->getResource() : FALSE) : $this->context;
  }
  
  /**
   * Set transport
   *
   * @param string $transport
   */
  public function setTransport($transport)
  {
    if ($this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set transport for socket. Socket is started.');
    }
    
    // Validate transport
    if (!self::isTransport($transport)) {
      throw new \InvalidArgumentException(sprintf('Undefined transport <b>%s</b>. Allowed transports: <b>%s</b>', $transport, implode('</b>, <b>', stream_get_transports())));
    }
    
    $this->transport = $transport;
    return $this;
  }
  
  /**
   * Get transport
   *
   * @return string
   *    Used transport
   */
  public function getTransport()
  {
    return $this->transport;
  }
  
  /**
   * Set target for connect of socket
   *
   * @param string $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
    return $this;
  }
  
  /**
   * Get target
   *
   * @param string $target
   */
  public function getTarget()
  {
    return $this->target;
  }
  
  /**
   * Set port
   * 
   * @param int $port
   */
  public function setPort($port)
  {
    // Validate port
    if (!(is_numeric($port) && strpos($port, '.') === FALSE)) {
      throw new \InvalidArgumentException(sprintf('Port must be integer value [Port:%s]', $port));
    }
    
    if ($port <= 0) {
      throw new \InvalidArgumentException(sprintf('Port must be large zero [Port:%s]', $port));
    }
    
    $this->port = $port;
    return $this;
  }
  
  /**
   * Get port
   * 
   * @return integer
   */
  public function getPort()
  {
    return $this;
  }
  
  /**
   * Set flags
   *
   * @param int $flags
   */
  public function setFlags($flags)
  {
    if (!in_array($flags, array(STREAM_CLIENT_CONNECT, STREAM_CLIENT_ASYNC_CONNECT, STREAM_CLIENT_PERSISTENT))) {
      throw new \InvalidArgumentException('Undefined flags in own system. Please check flags.');
    }
    
    $this->flags = $flags;
    return $this;
  }
  
  /**
   * Get flags
   *
   * @return int
   */
  public function getFlags()
  {
    return $this->flags;
  }
  
  /**
   * Get remove socket
   */
  public function getRemoteSocket()
  {
    if (!$this->transport) {
      throw new \InvalidArgumentException('Can\'t get remote socket. Undefined transport.');
    }
    
    if (!$this->target) {
      throw new \InvalidArgumentException('Can\'t get remote socket. Undefined target.');
    }
    
    if (!$this->port) {
      throw new \InvalidArgumentException('Can\'t get remote socket. Undefined port.');
    }
    
    return $this->transport . '://' . $this->target . ($this->port ? ':' . $this->port : '');
  }
  
  
  /**
   * Is socet started
   *
   * @param bool $autoload
   *    Autoload socket
   *
   * @return bool
   *    Status started socket
   */
  public function is($autoload = FALSE)
  {
    if (!$autoload) { return (bool) $this->resource; }
    else {
      if (!$this->resource) { $this->create(); }
      return (bool) $this->resource;
    }
  }
  
  /**
   * Create socket client
   */
  public function create()
  {
    if ($this->is(FALSE)) { return $this->resource; }
    if ($this->context) {
      $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flags, $this->getContext(TRUE));
    }
    else {
      $resource = @stream_socket_client($this->getRemoteSocket(), $errorCode, $errorStr, ini_get('default_socket_timeout'), $this->flags);
    }
    
    if (!$resource) {
      if (!$errorCode) {
        throw new SocketErrorException('Socket not create. Technical error in system.', 0);
      }
      else {
        throw new SocketErrorException($errorStr, $errorCode);
      }
    }
    
    $this->resource = $resource;
  }
  
  /**
   * Write to socket
   *
   * @param string $content
   * @param int $length
   *
   * @return bool
   *
   * @throws
   *    \RuntimeException
   */
  public function writeToSocket($content, $length = NULL)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t write to socket. Socket not created.');
    }
    
    if (is_null($length)) {
      return fwrite($this->getResource(), $content);
    }
    else {
      return fwrite($this->getResource(), $content, $length);
    }
  }
  
  /**
   * Read from socket
   *
   * @param int $length
   *
   * @return string
   *
   * @throws
   *    \RuntimeException
   */
  public function readFromSocket($length)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t read from socket. Socket not started.');
    }
    
    return fread($this->getResource(), $length);
  }
  
  /**
   * Is eof socket
   *
   * @return bool
   *
   * @throws
   *    \RuntimeException
   */
  public function isEofSocket()
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t read from socket. Socket not started');
    }
    
    return eof($this->getResource());
  }
  
  /**
   * Read all socket
   */
  public function readAllFromSocket()
  {
    return $this->getContents();
  }
}