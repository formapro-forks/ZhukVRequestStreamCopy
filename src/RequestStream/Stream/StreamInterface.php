<?php

namespace RequestStream\Stream;

/**
 * Interface for control request response
 */
interface StreamInterface {
  /**
   * Get stream transport
   *
   * @return string
   */
  public static function getTransports();
  
  /**
   * Is validate transport in system
   * 
   * @param string $transport
   * 
   * @return bool
   */
  public static function isTransport($transport);
  
  /**
   * Get all allowed wrappers in system
   *
   * @return array
   */
  public static function getWrappers();
  
  /**
   * Is wrapper allowed in system
   * 
   * @param string $wrapper
   *
   * @return bool
   */
  public static function isWrapper($wrapper);
  
  /**
   * Get full path file, included all paths
   *
   * @param string $path
   *
   * @return string
   */
  public static function resolveIncludePath($path);
  
  /**
   * Set encoding in stream
   *
   * @param string $encoding
   *
   * @return bool
   */
  public function setEncoding($encoding);
  
  /**
   * Get all contents from stream
   *
   * @param int $maxLength
   * @param int $offset
   *
   * @return string
   */
  public function getContents($maxLength = -1, $offset = -1);
  
  /**
   * Blocking stream
   *
   * @param int $mode
   *
   * @return bool
   */
  public function setBlocking($mode);
  
  /**
   * Set read buffer for stream
   *
   * @param int $buffer
   *
   * @return bool
   */
  public function setReadBuffer($buffer);
  
  /**
   * Set write buffer for stram
   *
   * @param int $buffer
   *
   * @return bool
   */
  public function setWriteBuffer($buffer);
  
  /**
   * Set timeout for stream
   *
   * @param int $second
   * @param int $milisecond
   *
   * @return bool
   */
  public function setTimeout($second, $milisecond = 0);
  
  /**
   * Is created stream
   *
   * @param bool $autoload      Is auto create stream
   *
   * @return bool
   */
  public function is($autoload = FALSE);
  
  /**
   * Create stream
   * {@inherit}
   */
  public function create();
  
  /**
   * Get resource of stream.
   *  If stream not opened, create strem.
   *
   * @return resource
   */
  public function getResource();
}
