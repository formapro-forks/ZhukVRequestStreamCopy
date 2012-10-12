<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream\Context;


/**
 * Interface for control context
 */
interface ContextInterface {
  /**
   * Get default options for content
   *
   * @return array
   */
  public static function getAllowedOptionsContext();
  
  /**
   * Get default context
   *
   * @return resource
   */
  public static function getDefault();
  
  /**
   * Get params from content
   *
   * @param resource $streamOrContent (Optional)
   *
   * @return array
   */
  public function getParams($streamOrContext = NULL);
  
  /**
   * Get options from content
   *
   * @param resource $streamOrContext (Optional)
   *
   * @return array
   */
  public function getOptions($streamOrContext = NULL);
  
  /**
   * Set options to context
   *
   * @param string $wrapper   Wrapper of stream (http, ssl, ftp, etc...)
   * @param string $paramName
   * @param string $paramValue
   *
   * @return null
   *    Exception, if not allowed wrapper in system,
   *    or not allowed variables for wrapper
   */
  public function setOptions($wrapper, $paramName = NULL,  $paramValue = NULL);
  
  /**
   * Set params to context
   *
   * @return null
   */
  public function setParams(array $params);
  
  /**
   * Is created content
   *
   * @param bool $autoload    If auto start create stream of context
   * @param array $options (Optionan)
   *    Options for create context. If $autoload = TRUE
   */
  public function is($autoload = FALSE, array $options = array());
  
  /**
   * Create stream of context
   *
   * @param array $options
   * @param array $params
   */
  public function create(array $options = array(), array $params = array());
}