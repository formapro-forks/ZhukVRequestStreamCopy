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
abstract class StreamAbstract implements StreamInterface
{
  // Resource stram
  protected $resource = NULL;

  /**
   * @{inerhitDoc}
   */
  public static function getTransports()
  {
    return stream_get_transports();
  }

  /**
   * @{inerhitDoc}
   */
  public static function isTransport($transport)
  {
    return in_array($transport, self::getTransports());
  }

  /**
   * @{inerhitDoc}
   */
  public static function getWrappers()
  {
    return stream_get_wrappers();
  }

  /**
   * @{inerhitDoc}
   */
  public static function isWrapper($wrapper)
  {
    return in_array($wrapper, self::getWrappers());
  }

  /**
   * @{inerhitDoc}
   */
  public static function resolveIncludePath($file)
  {
    return stream_resolve_include_path($file);
  }

  /**
   * @{inerhitDoc}
   */
  public function setEncoding($encodig)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set encoding for stream. Stream not created.');
    }

    return stream_encoding($this->getResource(), $encodig);
  }

  /**
   * @{inerhitDoc}
   */
  public function getContents($maxLength = -1, $offset = -1)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get content. Stream not created.');
    }

    return stream_get_contents($this->getResource(), $maxLength, $offset);
  }

  /**
   * @{inerhitDoc}
   */
  public function getLine($length, $ending = "\n")
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get line. Stream not created.');
    }

    return stream_get_line($this->getResource(), $length, $ending);
  }

  /**
   * @{inerhitDoc}
   */
  public function setBlocking($mode)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set blocking of mode stream. Stream not created.');
    }

    return stream_set_blocking($this->getResource(), $mode);
  }

  /**
   * @{inerhitDoc}
   */
  public function setReadBuffer($buffer)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set read buffer. Stream not created.');
    }

    return stream_set_read_buffer($this->getResource(), $buffer);
  }

  /**
   * @{inerhitDoc}
   */
  public function setWriteBuffer($buffer)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set write buffer. Stream not created.');
    }

    return stream_set_write_buffer($this->getResource(), $buffer);
  }

  /**
   * @{inerhitDoc}
   */
  public function setTimeout($second, $milisecond = 0)
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t set timetout. Stream not created.');
    }

    return stream_set_timeout($this->getResource(), $second, $milisecond);
  }

  /**
   * @{inerhitDoc}
   */
  public function getMetaData()
  {
    if (!$this->is(FALSE)) {
      throw new \RuntimeException('Can\'t get meta data. Stream not created.');
    }

    return stream_get_meta_data($this->getResource());
  }

  /**
   * @{inerhitDoc}
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
   * @{inerhitDoc}
   */
  public function getResource()
  {
    if (is_null($this->resource)) {
      $this->create();
    }

    return $this->resource;
  }
}