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

use RequestStream\Request\Exception\ResultException;


/**
 * Base core for control result web request
 */
class Result implements ResultInterface {
  // Data
  protected $data = NULL;
  
  // Headers
  protected $headers = array();
  
  // Code
  protected $code = NULL;
  
  // Response
  protected $response = NULL;
  
  // Protocol
  protected $protocol = NULL;
  
  // Cookies
  protected $cookies = array();
  
  /**
   * Construct
   */
  public function __construct()
  {
    
  }
  
  /**
   * Get data
   */
  public function getData()
  {
    return $this->data;
  }
  
  /**
   * Get headers
   *
   * @param string $name
   */
  public function getHeaders($name = NULL)
  {
    return $name ? (isset($this->headers[$name]) ? $this->headers[$name] : NULL) : $this->headers;
  }
  
  /**
   * Get code
   */
  public function getCode()
  {
    return $this->code;
  }
  
  /**
   * Get response
   */
  public function getResponse()
  {
    return $this->response;
  }
  
  /**
   * Get cookie
   *
   * @param string $name
   */
  public function getCookies($name = NULL)
  {
    return $name ? (isset($this->cookies[$name]) ? $this->cookies[$name] : NULL) : $this->cookies;
  }
  
  /**
   * Is cookie
   *
   * @param string $name
   */
  public function isCookie($name = NULL)
  {
    return $name ? isset($this->cookies[$name]) : (bool) $this->cookies;
  }
  
  /**
   * Parse page content
   *
   * @param string $content
   */
  public function parsePageContent($content)
  {
    $content = explode("\r\n\r\n", $content, 2);
    
    if (!count($content)) {
      throw new ResultException('Can\'t parse page content. Not found header or page section.');
    }
    $this->parsePageHeaders($content[0]);
    
    //if ($contentType = $this->getHeaders('Content-Type')) {
    //  @list($null, $charset) = explode('=', rtrim($contentType));
    //  
    //  if (mb_strtolower($charset) != 'UTF-8') {
    //    $content[1] = mb_convert_encoding($content[1], 'UTF-8', mb_detect_encoding($content[1]));
    //  }
    //}
    
    $this->data = @$content[1];
    
  }
  
  /**
   * Parse headers
   * 
   * @param string $headerContent
   */
  protected function parsePageHeaders($headerContent)
  {
    $headers = preg_split("/\r\n|\r|\n/", $headerContent);
    
    @list ($this->protocol, $this->code, $text) = explode(' ', $headers[0]);
    unset ($headers[0]);
    
    $responses = array(
      100 => 'Continue', 101 => 'Switching Protocols',
      200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
      300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 307 => 'Temporary Redirect',
      400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed',
      500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported'
    );
    
    $this->response = $responses[$this->code];
    
    foreach ($headers as $h) {
      list ($key, $value) = explode(':', $h, 2);
      if ($key == 'Set-Cookie') {
        if (!isset($this->headers['Set-Cookie'])) { $this->headers['Set-Cookie'] = array(); }
        $cookie = self::parseCookie($value);
        $cookieName = $cookie['name'];
        $this->headers['Set-Cookie'][$cookieName] = $cookie['value'];
        unset ($cookie['name']);
        $this->cookies[$cookieName] = $cookie;
      }
      else {
        $this->headers[trim($key)] = trim($value);
      }
    }
  }
  
  /**
   * Parse cookie
   */
  public static function parseCookie($cookieStr)
  {
    // Get base values from cookie string
    @list ($value, $expires, $path, $domain, $secure, $httpOnly) = explode(';', $cookieStr);
    
    // Get name, value, path etc... from cookie item
    @list ($name, $value) = explode('=', trim($value));
    @list ($null, $expires) = explode('=', trim($expires));
    @list ($null, $path) = explode('=', trim($path));
    @list ($null, $domain) = explode('=', trim($domain));
    
    // If not added expires to set cookie
    if ($expires == '/') {
      $expires = NULL; $path = '/';
    }
    
    return array(
      'name' => $name,
      'value' => $value,
      'expires' => $expires,
      'path' => $path,
      'domain' => ltrim($domain, '.'),
      'secure' => (bool) $secure,
      'http_only' => (bool) $httpOnly
    );
  }
  
  /**
   * Get cookie by domain
   */
  public function getCookiesByFilter(array $filters)
  {
    $filters += array(
      'domain' => NULL,
      'path' => NULL,
      'expires' => NULL
    );
    
    $coks = array();
    
    foreach ($this->cookies as $cName => $cValue) {
      // Filter domain
      if ($filters['domain'] && ($cValue['domain'] && ltrim($filters['domain'], '.') != ltrim($cValue['domain'], '.')) ) {
        continue;
      }
      
      // Here code for filter: path, expires, etc...
      $coks[$cName] = $cValue;
    }
    
    return $coks;
  }
}