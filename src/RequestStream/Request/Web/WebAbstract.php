<?php


namespace RequestStream\Request\Web;

use RequestStream\Request\Exception\UriException,
    RequestStream\Request\Exception\HeadersException,
    RequestStream\Request\Exception\RequestException,
    RequestStream\Request\Exception\ResultException,
    RequestStream\Request\Exception\RedirectException,
    RequestStream\Request\Web\Result,
    RequestStream\Stream\Context\Context;

/**
 * Abstract core for web request
 */
abstract class WebAbstract implements WebInterface {
  // Proxy
  protected $proxy = NULL;
  
  // Method
  protected $method = 'GET';

  // User agent
  protected $user_agent = NULL;

  // Cookies
  protected $cookies = array();

  // Post data
  protected $post_data = array();

  // Headers
  protected $headers = array();

  // Uri
  protected $uri = NULL;

  // User login
  protected $user_login = array('user' => NULL, 'pass' => NULL);

  // HTTP Version
  protected $http_version = '1.0';

  // Result
  protected $result = NULL;

  // Sending request
  protected $sending_request = FALSE;

  // Count redirect
  protected $count_redirect = 5;

  // Count use redirect
  protected $count_use_redirect = 0;

  // Use redirect cookies
  protected $redirect_use_cookie = TRUE;

  // Boudary
  private $_boundary = NULL;

  /**
   * Construct
   */
  public function __construct($uri = NULL)
  {
    if ($uri) {
      $this->setUri($uri);
    }
  }
  
  
  /**
   * Set proxy
   *
   * @param string $uri
   *
   * @return this object
   */
  public function setProxy($uri)
  {
    $this->proxy = $this->parseUri($uri);
    return $this;
  }
  
  /**
   * Get proxy
   */
  public function getProxy()
  {
    return $this->proxy;
  }

  /**
   * Set method
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $method = strtoupper($method);

    if (!in_array($method, array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT'))) {
      throw new \InvalidArgumentException(sprintf('Undefined method <b>%s</b>.', $method));
    }

    $this->method = $method;
    return $this;
  }

  /**
   * Get method
   */
  public function getMethod()
  {
    return $this->method;
  }

  /**
   * Set user agent
   *
   * @param string $user_agent
   */
  public function setUserAgent($user_agent)
  {
    $this->user_agent = $user_agent;
    return $this;
  }

  /**
   * Get user agent
   */
  public function getUserAgent()
  {
    return $this->user_agent;
  }

  /**
   * Set random user agent
   */
  public function setUserAgentRandom()
  {
    $this->user_agent = $this->generateRandomUserAgent();
  }

  /**
   * Set cookies
   *
   * @param array $cookies
   */
  public function setCookies(array $cookies)
  {
    $this->cookies = $cookies;
    return $this;
  }

  /**
   * Set one cookie
   *
   * @param string $name
   *
   * @param mixed $value
   */
  public function addCookie($name, $value = NULL)
  {
    if (is_array($name)) {
      $this->cookies = array_merge($this->cookies, $name);
    }
    else {
      $this->cookies[$name] = $value;
    }

    return $this;
  }

  /**
   * Delete cookie
   *
   * @param string $name
   */
  public function deleteCookie($name = NULL)
  {
    if (is_null($name)) {
      $this->cookies = array();
    }
    else {
      unset ($this->cookies[$name]);
    }

    return $this;
  }

  /**
   * Set post data
   *
   * @param array $data
   */
  public function setPostData(array $data)
  {
    $this->post_data = $data;
    return $this;
  }

  /**
   * Add post data
   *
   * @param string $name
   *
   * @param string $value
   */
  public function addPostData($name, $value = NULL)
  {
    if (is_array($name)) {
      $this->post_data = array_merge($this->post_data, $name);
    }
    else {
      $this->post_data[$name] = $value;
    }

    return $this;
  }

  /**
   * Delete post data
   *
   * @param string $name
   */
  public function deletePostData($name = NULL)
  {
    if (is_null($name)) {
      $this->post_data = array();
    }
    else {
      unset ($this->post_data[$name]);
    }

    return $this;
  }

  /**
   * Set headers
   *
   * @param array $headers
   */
  public function setHeaders(array $headers)
  {
    return $this->addHeader($headers);
  }

  /**
   * Set one header
   *
   * @param string $name
   *
   * @param mixed $value
   */
  public function addHeader($name, $value = NULL)
  {
    if (!is_array($name)) {
      $name = array($name => $value);
    }

    foreach ($name as $nameHeader => $valueHeader) {
      $smallNameHeader = strtolower($nameHeader);
      switch ($smallNameHeader) {
        case 'user_agent':
        case 'user-agent':
          $this->setUserAgent($valueHeader);
          break;

        case 'cookie':
          if (is_array($valueHeader)) {
            $this->setCookies($valueHeader);
          }
          else if (is_string($valueHeader)) {
            // Here code for parse string
          }
          else if (is_object($valueHeader) && method_exists($valueHeader, '__toString')) {
            // Here code for compile object to string and parse string
          }
          break;

        default:
          $this->headers[$nameHeader] = $valueHeader;
      }
    }

    return $this;
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
   * Delete header
   *
   * @param string $name
   */
  public function deleteHeader($name = NULL)
  {
    if (is_null($name)) {
      $this->headers = array();
    }

    unset ($this->headers[$name]);
    return $this;
  }

  /**
   * Set uri
   */
  public function setUri($uri)
  {
    $this->uri = $this->parseUri($uri);

    // Set user login (Basic authorization)
    if ($this->uri['user']) {
      $this->setUserPass($this->uri['user'], $this->uri['pass']);
    }

    return $this;
  }

  /**
   * Set login/password for request
   *
   * @param string $user
   *
   * @param string $password
   */
  public function setUserPass($user, $pass)
  {
    $this->user_login = array('user' => $user, 'pass' => $pass);
  }

  /**
   * Set HTTP Version
   *
   * @param string $version
   */
  public function setHttpVersion($version)
  {
    $this->http_version = $version;
    return $this;
  }

  /**
   * Get HTTP Version
   *
   * @return float
   */
  public function getHttpVersion()
  {
    return $this->http_version;
  }

  /**
   * Set referer
   *
   * @param string $referer
   */
  public function setReferer($referer)
  {
    try {
      $parseUri = $this->parseUri($referer);
    }
    catch(\Exception $e) {
      throw new UriException('Can\'t set referer. Error of parse uri: ' . $e->getMessage());
    }

    $referer = $parseUri['scheme'] . '://' . $parseUri['host'] . $parseUri['path'] . ($parseUri['query'] ? '?' . $parseUri['query'] : '');
    $this->headers['Referer'] = $referer;
  }

  /**
   * Set count redirect
   *
   * @param integer $count
   */
  public function setCountRedirect($count = 0)
  {
    $this->count_redirect = (int) $count;
    return $this;
  }

  /**
   * Set use redirect cookie
   *
   * @param bool $status
   */
  public function setRedirectCookie($status = TRUE)
  {
    $this->redirect_use_cookie = (bool) $status;
    return $this;
  }

  /**
   * Parse uri
   */
  protected function parseUri($uri)
  {
    if (preg_match('/^([a-z]{0,5}):\/\//', $uri, $tmp)) {
      if (!in_array($tmp[1], array('http', 'https'))) {
        throw new UriException(sprintf('Uri must be beginning from <b>http</b> or <b>https</b> (Beginning with: <b>%s</b>)', $tmp[1]));
      }
    }
    else {
      $uri = 'http://' . $uri;
    }

    if (!$parseUri = @parse_url($uri)) {
      throw new \InvalidArgumentException(sprintf('Can\'t parse uri <b>%s</b>. Please check uri!', $uri));
    }
    
    if (strpos($parseUri['host'], '.') === FALSE) {
      throw new \InvalidArgumentException(sprintf('Can\'t parse uri <b>%s</b>. Please check uri!', $uri));
    }
    
    $parseUri += array(
      'user' => NULL,
      'pass' => NULL,
      'fragment' => NULL,
      'query' => NULL,
      'uri' => $uri,
      'port' => NULL,
      'path' => '/'
    );

    //$parseUri['path'] = rtrim($parseUri['path'], '/');

    if ($parseUri['query']) {
      $qi = explode('&', $parseUri['query']);

      // Query encode for URL
      array_walk($qi, function(&$q){
        @list( $name, $param ) = explode( '=', $q, 2 );
        $q = ( is_null( $param ) ) ? urlencode( $name ) : urlencode( $name ) . '=' . urlencode( $param );
      });

      $parseUri['query'] = implode('&', $qi);
    }

    return $parseUri;
  }

  /**
   * Generate random user agent
   */
  public function generateRandomUserAgent( $options = array(  ) )
  {
    //Possible processors on Linux
    $linux_proc = array( 'i686', 'x86_64' );

    //Mac processors (i also added U;)
    $mac_proc   = array( 'Intel', 'PPC', 'U; Intel', 'U; PPC' );

    //Add as many languages as you like.
    $lang = array(
      'ru',
      'ru-RU',
    );

    $a_browser = array( 'Firefox', 'Opera', 'Chrome', 'IE', 'Safari' );
    //$options['browser'] = array( 'Opera' );
    if (isset($options['browser'])){
      if (!array_diff($options['browser'], $a_browser))
        $type = $options['browser'][array_rand( $options['browser'] )];
    }

    if (!isset( $type )) {
      $type = $a_browser[array_rand($a_browser)];
    }

    switch ( $type ){
      case 'Firefox':
        // Generate Mozilla Firefox agent
        date_default_timezone_set( 'Europe/Helsinki' );
        $ver = array(
          date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0',
          date('Ymd', rand(strtotime('2011-1-1'), time())) . ' Firefox/' . rand(5, 7) . '.0.1',
          date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.6.' . rand(1, 20),
          date('Ymd', rand(strtotime('2010-1-1'), time())) . ' Firefox/3.8'
        );

        $platforms = array(
          '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; ' . $lang[array_rand($lang, 1)] . '; rv:1.9.' . rand(0, 2) . '.20) Gecko/' . $ver[array_rand($ver, 1)],
          '(X11; Linux ' . $linux_proc[array_rand($linux_proc, 1)] . '; rv:' . rand(5, 7) . '.0) Gecko/' . $ver[array_rand($ver, 1)],
          '(Macintosh; ' . $mac_proc[array_rand($mac_proc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ' rv:' . rand(2, 6) . '.0) Gecko/' . $ver[array_rand($ver, 1)]
        );

        $ua = "Mozilla/5.0 " . $platforms[array_rand($platforms, 1)];
        break;
      
      case 'Safari':
        // Generate Safari agent
        $saf = rand(531, 535) . '.' . rand(1, 50) . '.' . rand(1, 7);
        $ver = (rand(0, 1) == 0)
                ? rand(4, 5) . '.' . rand(0, 1)
                : $ver = rand(4, 5) . '.0.' . rand(1, 5);

        $platforms = array(
          '(Windows; U; Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/{$ver} Safari/{$saf}",
          '(Macintosh; U; ' . $mac_proc[array_rand($mac_proc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ' rv:' . rand(2, 6) . '.0; ' . $lang[array_rand($lang, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/{$ver} Safari/{$saf}",
          '(iPod; U; CPU iPhone OS ' . rand(3, 4) . '_' . rand(0, 3) . ' like Mac OS X; ' . $lang[array_rand($lang, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Version/" . rand(3, 4) . ".0.5 Mobile/8B" . rand(111, 119) . " Safari/6{$saf}",
        );

        $ua = "Mozilla/5.0 " . $platforms[array_rand($platforms, 1)];
        break;
      
      case 'IE':
        // Generate Internet Explorer agent
        $platforms = array(
          '(compatible; MSIE ' . rand(5, 9) . '.0; Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; Trident/' . rand(3, 5) . '.' . rand(0, 1) . ')'
        );

        $ua = "Mozilla/" . rand(4, 5) . ".0 " . $platforms[array_rand($platforms, 1)];
        break;
      case 'Opera':
        // Generate Opera agent
        $platforms = array(
          '(X11; Linux ' . $linux_proc[array_rand($linux_proc, 1)] . '; U; ' . $lang[array_rand($lang, 1)] . ') Presto/2.9.' . rand(160, 190) . ' Version/' . rand(10, 12) . '.00',
          '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . '; U; ' . $lang[array_rand($lang, 1)] . ') Presto/2.9.' . rand(160, 190) . ' Version/' . rand(10, 12) . '.00'
        );
        $ua = "Opera/9." . rand(10, 99) . ' ' . $platforms[array_rand($platforms, 1)];
        break;
      case 'Chrome':
        // Generate Chrome agent
        $saf = rand(531, 536) . rand(0, 2);

        $platforms = array(
          '(X11; Linux ' . $linux_proc[array_rand($linux_proc, 1)] . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/$saf",
          '(Windows NT ' . rand(5, 6) . '.' . rand(0, 1) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/{$saf}",
          '(Macintosh; U; ' . $mac_proc[array_rand($mac_proc, 1)] . ' Mac OS X 10_' . rand(5, 7) . '_' . rand(0, 9) . ") AppleWebKit/{$saf} (KHTML, like Gecko) Chrome/" . rand(13, 15) . '.0.' . rand(800, 899) . ".0 Safari/{$saf}"
        );

        $ua = 'Mozilla/5.0' . $platforms[array_rand($platforms, 1)];
        break;
    }
    
    return $ua;
  }

  /**
   * Generate boundary for POST
   */
  protected function getPostBoundary($reset = FALSE)
  {
    if ($this->_boundary && !$reset) {
      return $this->_boundary;
    }

    $this->_boundary = 'Asrf456BGe4h';
    return $this->_boundary;
  }

  /**
   * Prepare headers
   */
  protected function prepareHeaders()
  {
    // Added Accept header if not exists
    if (!$this->getHeaders('Accept')) {
      $this->addHeader('Accept', '*/*');
    }

    // Added user agent
    if ($this->getUserAgent()) {
      // Use link to headers var, becouse addHeader have recursioon to setUserAgent
      $this->headers['User-Agent'] = $this->getUserAgent();
    }
  }

  /**
   * Get cookies string for send
   */
  protected function getHeaderCookiesString()
  {
    if (!$this->cookies) { return; }

    $cookieStr = array();

    foreach ($this->cookies as $cookieName => $cookieValue) {
      if (is_array($cookieValue)) {
        $cookieStr[$cookieName] = $cookieValue['value'];
      }
      else {
        $cookieStr[$cookieName] = $cookieValue;
      }
    }

    // Generate: key=value
    array_walk($cookieStr, function(&$v, $k) {
      $v = $k . '=' . $v;
    });

    return implode(';', $cookieStr);
  }

  /**
   * Get result
   *  Alias for method sendRequest
   */
  public function getResult($reset = FALSE)
  {
    return $this->sendRequest($reset);
  }

  /**
   * Send request to remote address
   */
  final function sendRequest($reset = FALSE)
  {
    if (!$this->uri) {
      throw new RequestException('Can\'t send request to remote address. Undefined target uri.');
    }

    if ($this->sending_request && !$reset) { return $this->result; }

    // Set status sending request
    $this->sending_request = TRUE;

    // Reset count use redirect
    if (!$reset) {
      $this->count_use_redirect = 0;
    }

    // Create request
    $this->createRequest();

    if (!$this->result instanceof ResultInterface) {
      throw new ResultException('Can\'t get result. Result must be instance of ResultInterface.');
    }

    // If moved (Location)
    if (in_array($this->result->getCode(), array(301, 302, 307)) && $this->result->getHeaders('Location')) {

      // Call to hook
      if (method_exists($this, 'notificationRedirect')) {
        $this->notificationSendRedirect();
      }

      return $this->sendRequestRedirect();
    }

    return $this->result;
  }

  /**
   * Create request redirect
   */
  protected function sendRequestRedirect()
  {
    // Create a new socket connection and new request
    if ($this->count_redirect && $this->count_use_redirect >= $this->count_redirect) {
      throw new RedirectException(sprintf('Many redirect: <b>%s</b>', $this->count_use_redirect));
    }

    $this->count_use_redirect++;

    $refererUri = $this->uri['uri'];
    $this->setReferer(urldecode($refererUri));

    // Generate and set location to
    $locationTo = $this->result->getHeaders('Location');
    if (strpos($locationTo, '/') === 0) {
      $locationTo = $this->uri['scheme'] . '://' . $this->uri['host'] . $locationTo;
    }
    else if (strpos($locationTo, 'http') === FALSE) {
      $locationTo = $this->uri['scheme'] . '://' . $this->uri['host'] . rtrim($this->uri['path']) . '/' . $locationTo;
    }

    $this->setUri($locationTo);

    // If used save cookie for redirect
    if ($this->redirect_use_cookie) {
      $setCookies = $this->result->getCookiesByFilter(array(
        'domain' => $this->uri['host']
      ));

      $this->addCookie($setCookies);
    }

    // Reset post data
    $this->setPostData(array());
    
    // Reset request method type
    $this->setMethod('GET');

    return $this->sendRequest(TRUE);
  }


  /**
   * Create and sending first request
   */
  abstract protected function createRequest();

  /**
   * Parse page content
   *
   * @param string $content
   */
  protected function generateResult($content) {
    $this->result = new Result;
    $this->result->parsePageContent($content);
  }
}