<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream;


/**
 * Abstract core for use stream
 */
abstract class StreamAbstract implements StreamInterface {
  
  // Resource stram
  protected $resource = NULL;
  
  /**
   * Get all transports in system
   *
   * @return array
   */
  public static function getTransports()
  {
    return stream_get_transports();
  }
  
  /**
   * Is transport in system
   *
   * @param string $transport
   * 
   * @return bool
   *    Status allowed transport in system
   */
  public static function isTransport($transport)
  {
    return in_array($transport, self::getTransports());
  }
  
  /**
   * Get all wrappers in system
   */
  public static function getWrappers()
  {
    return stream_get_wrappers();
  }
  
  /**
   * Is wrapper in system
   *
   * @param string $wrapper
   *
   * @return bool
   *    Status allowed wrapped in system
   */
  public static function isWrapper($wrapper)
  {
    return in_array($wrapper, self::getWrappers());
  }
  
  /**
   * Resolve include path
   *
   * @param string $file
   */
  public static function resolveIncludePath($file)
  {
    return stream_resolve_include_path($file);
  }
  
  
  /**
   * Set encoding
   *
   * @param string $encodig
   *    Encoding for stream
   */
  public function setEncoding($encodig)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set encoding for stream. Stream not created.');
    }
    
    return stream_encoding($this->getResource(), $encodig);
  }
  
  /**
   * Get contents
   * @see http://www.php.net/manual/en/function.stream-get-contents.php
   
   * @param int $maxLength
   *    Max length from stream
   *
   * @param int $offset
   *    Offset
   *
   * @return string or bool
   *    FALSE if error
   */
  public function getContents($maxLength = -1, $offset = -1)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get content. Stream not created.');
    }
    
    return stream_get_contents($this->getResource(), $maxLength, $offset);
  }
  
  /**
   * Get line
   * @see http://www.php.net/manual/ru/function.stream-get-line.php
   *
   * @param int $length
   *
   * @param string $ending
   */
  public function getLine($length, $ending = "\n")
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get line. Stream not created.');
    }
    
    return stream_get_line($this->getResource(), $length, $ending);
  }
  
  /**
   * Set blocking
   * @param int mode
   */
  public function setBlocking($mode)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set blocking of mode stream. Stream not created.');
    }
    
    return stream_set_blocking($this->getResource(), $mode);
  }
  
  /**
   * Set read buffer for stream
   *
   * @param int $buffer
   */
  public function setReadBuffer($buffer)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set read buffer. Stream not created.');
    }
    
    return stream_set_read_buffer($this->getResource(), $buffer);
  }
  
  /**
   * Set write buffer for stream
   *
   * @param int $buffer
   */
  public function setWriteBuffer($buffer)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set write buffer. Stream not created.');
    }
    
    return stream_set_write_buffer($this->getResource(), $buffer);
  }
  
  /**
   * Set timeout
   *
   * @param int $second
   *    Second for timeout
   *
   * @param int $milisecond
   *    Milisecond for timeout
   */
  public function setTimeout($second, $milisecond = 0)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set timetout. Stream not created.');
    }
    
    return stream_set_timeout($this->getResource(), $second, $milisecond);
  }
  
  /**
   * Get meta data
   */
  public function getMetaData()
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get meta data. Stream not created.');
    }
    
    return stream_get_meta_data($this->getResource());
  }

  /**
   * Is started stream resource
   *
   * @param bool $autoload
   *
   * @return bool
   *    Status creating resource
   */
  public function is($autoload = FALSE)
  {
    if (!$autoload) { return (bool) $this->resource; }
    
    // If started stream throw new exception
    // Must be use try operand
    try{
      $this->create();
    }
    catch (\Exception $e) {
      return FALSE;
    }
    
    return (bool) $this->resource;
  }
  
  /**
   * Create resource for stream
   */
  public function create()
  {
    // Code here for creating stream resource
    
  }
  
  /**
   * Get resource of stream
   */
  public function getResource()
  {
    if (is_null($this->resource)) {
      $this->create();
    }
    
    return $this->resource;
  }
}