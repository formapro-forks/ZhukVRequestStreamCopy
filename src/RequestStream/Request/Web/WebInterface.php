<?php

namespace RequestStream\Request\Web;

/**
 * Interface for control web request
 */
interface WebInterface {
  /**
   * Set proxy
   *
   * @param string $uri
   *
   * @return this object
   */
  public function setProxy($uri);
  
  /**
   * Get proxy
   *
   * @return string
   */
  public function getProxy();
  
  /**
   * Set method foe request (GET, POST, HEAD, etc...)
   *
   * @param string $method
   *
   * @return this object
   *    Exception, if method not allowed
   */
  public function setMethod($method);
  
  /**
   * Get used method
   *
   * @return string
   */
  public function getMethod();
  
  /**
   * Set user agent
   *
   * @param string $user_agent
   *
   * @return this object
   */
  public function setUserAgent($user_agent);
  
  /**
   * Get used user agent
   *
   * @return string
   */
  public function getUserAgent();
  
  /**
   * Set random user agent
   *    Generate user agent
   *
   * @return this object
   */
  public function setUserAgentRandom();
  
  /**
   * Set cookies for request
   *
   * @param array $cookies
   *
   * @return this object
   */
  public function setCookies(array $cookie);
  
  /**
   * Set item cookie
   *
   * @param string $name
   * @param string $value (Optional)
   *
   * @return this object
   */
  public function addCookie($name, $value = NULL);
  
  /**
   * Delete cookie from request
   *
   * @param string $name
   */
  public function deleteCookie($name = NULL);
  
  /**
   * Set post data
   *
   * @param array $data
   *
   * @return this object
   */
  
  public function setPostData(array $data);
  
  /**
   * Add one item to post data
   *
   * @param string|array $name
   * @param mixed $value
   *
   * @return this object
   */
  public function addPostData($name, $value = NULL);
  
  /**
   * Delete post data
   *
   * @param string $name
   *
   * @return this object
   */
  public function deletePostData($name = NULL);
  
  /**
   * Set headers
   *
   * @param array $headers
   *
   * @return this object
   */
  public function setHeaders(array $headers);
  
  /**
   * Add header
   *
   * @param string|array $name
   * @param mixed $value
   *
   * @return this object
   */
  public function addHeader($name, $value = NULL);
  
  /**
   * Get headers
   *
   * @param string $name
   *
   * @return mixed
   */
  public function getHeaders($name = NULL);
  
  /**
   * Delete headers
   *
   * @param string $name
   *
   * @return this object
   */
  public function deleteHeader($name = NULL);
  
  /**
   * Set uri for request
   *
   * @param string $uri
   *
   * @return this object
   *
   * @throws Exception\UriException
   */
  public function setUri($uri);
  
  /**
   * Set user password for connect
   *
   * @param string $user
   * @param string $password
   *
   * @return this object
   */
  public function setUserPass($user, $password);
  
  /**
   * Set HTTP Version protocol
   *
   * @param string $version
   *
   * @return this object
   */
  public function setHttpVersion($version);
  
  /**
   * Get HTT Version
   *
   * @return string
   */
  public function getHttpVersion();
  
  /**
   * Set referer for request
   *
   * @param string $uri
   *
   * @return this object
   */
  public function setReferer($uri);
  
  /**
   * Set max count redirect
   *
   * @param int $count
   *
   * @return this object
   */
  public function setCountRedirect($count = 0);
  
  /**
   * Set using cookie in redirected
   *
   * @param bool $status
   *
   * @return this object
   */
  public function setRedirectCookie($status = TRUE);
  
  /**
   * Send request
   *
   * @param bool $reset
   *
   * @return Result object
   */
  public function sendRequest($reset = FALSE);
  
  /**
   * Get result request
   *
   * @param bool $reset
   *
   * @return Result object
   */
  public function getResult($reset = FALSE);
}
