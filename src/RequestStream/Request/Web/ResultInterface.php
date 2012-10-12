<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

/**
 * Interface for control result
 */
interface ResultInterface {
  /**
   * Get result data
   *
   * @return string
   */
  public function getData();
  
  /**
   * Get headers
   *
   * @param string $name (Optional)
   *
   * @return array|string
   *    If used $name, return header of name
   */
  public function getHeaders($name = NULL);
  
  /**
   * Get code status (Status server)
   *
   * @return int
   */
  public function getCode();
  
  /**
   * Get response server
   *
   * @return string
   */
  public function getResponse();
  
  /**
   * Get cookies
   *
   * @param string $name
   *
   * @return array
   */
  public function getCookies($name = NULL);
  
  /**
   * Is cookie
   *
   * @param string $name
   *
   * @return bool
   */
  public function isCookie($name = NULL);
  
  /**
   * Parse page content
   *
   * @param string $content
   * 
   * @return array
   *    - 0 => Headers content
   *    - 1 => Page content
   *
   *    Exception, if not allowed formats
   */
  public function parsePageContent($content);
  
  /**
   * Parse cookie string
   *
   * @param string $cookie
   *
   * @return array
   *    - name (string) => Name of cookie
   *    - value (string)
   *    - domain (string)
   *    - expire (string)
   *    - securi (bool)
   *    - http_only (bool)
   */
  public static function parseCookie($cookie);
  
  /**
   * Get cookie by filters
   *
   * @param array $filters
   *
   * @return array
   */
  public function getCookiesByFilter(array $filters);
}