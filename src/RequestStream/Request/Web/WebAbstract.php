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

use RequestStream\Request\Exception\UriException,
    RequestStream\Request\Exception\HeadersException,
    RequestStream\Request\Exception\RequestException,
    RequestStream\Request\Exception\ResultException,
    RequestStream\Request\Exception\RedirectException,
    RequestException\Request\Exception\XmlDataException,
    RequestStream\Request\Web\Result,
    RequestStream\Stream\Context\Context;

/**
 * Abstract core for web request
 */
abstract class WebAbstract implements WebInterface
{
    /**
     * @var Proxy
     */
    protected $proxy = NULL;

    /**
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var string
     */
    protected $userAgent = NULL;

    /**
     * @var CookiesBag
     */
    protected $cookies = array();

    // Post data
    protected $post_data = array();

    // Xml data
    protected $xml_data = NULL;

    /**
     * @var HeadersBag
     */
    protected $headers = array();

    /**
     * @var Uri
     */
    protected $uri = NULL;

    /**
     * @var array
     */
    protected $userLogin = array();

    /**
     * @var string
     */
    protected $httpVersion = '1.0';

    /**
     * @var Result
     */
    protected $result;

    // Sending request
    protected $sending_request = FALSE;

    /**
     * @var integer
     */
    protected $countRedirect = 5;

    /**
     * @var boolean
     */
    protected $redirectUseCookie = TRUE;

    /**
     * @var string
     */
    private $_boundary = NULL;

    /**
     * Construct
     *
     * @param string|Uri $uri
     */
    public function __construct($uri = NULL)
    {
        if ($uri) {
            $this->setUri($uri);
        }

        $this->cookies = new CookiesBag();
        $this->headers = new HeadersBag();
        $this->userLogin = array('user' => NULL, 'pass' => NULL);
    }

    /**
     * @{inerhitDoc}
     */
    public function setProxy($proxy)
    {
        if ($proxy instanceof Proxy) {
            $this->proxy = $proxy;
        }
        else {
            try {
                $this->proxy = Proxy::parseFromString($proxy);
            }
            catch (\Exception $e) {
                throw new \RuntimeException('Can\'t set proxy core.', 0, $e);
            }
        }

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * @{inerhitDoc}
     */
    public function setMethod($method)
    {
        $method = strtoupper($method);

        $allowedMethods = array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'TRACE', 'LINK', 'UNLINK', 'CONNECT');
        if (!in_array($method, $allowedMethods)) {
            throw new \InvalidArgumentException(sprintf(
                'Undefined method "%s". Allowed methods: "%s"',
                $method,
                implode('", "', $allowedMethods)
            ));
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @{inerhitDoc}
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @{inerhitDoc}
     */
    public function setUserAgentRandom($browser = NULL)
    {
        $this->userAgent = UserAgentGenerator::generateUserAgent($browser);
    }

    /**
     * @{inerhitDoc}
     */
    public function setCookies($cookies)
    {
        if (!is_array($cookies) && !$cookies instanceof \Iterator && !$cookies instanceof CookiesBag) {
            throw new \InvalidArgumentException('Cookies must be CookiesBag object or iterable.');
        }

        if ($cookies instanceof CookiesBag) {
            $this->cookies = $cookies;
            return $this;
        }

        $this->cookies = new CookiesBag;

        foreach ($cookies as $cookieName => $cookie) {
            if ($cookie instanceof Cookie) {
                $this->addCookie($cookie);
            }
            else {
                $this->addCookie($cookieName, $cookie);
            }
        }

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function addCookie($name, $value = NULL)
    {
        // Itarable
        if ($name instanceof \Iterator || is_array($name)) {
            foreach ($name as $cookieName => $cookie) {
                $this->addCookie($cookieName, $cookie);
            }
            return $this;
        }

        // Cookie object
        if ($name instanceof Cookie) {
            $this->cookies[$name->getName()] = $name;
        }
        else if ($value instanceof Cookie) {
            $this->cookies[$value->getName()] = $value;
        }
        else {
            // Create new cookie object
            $this->cookies[$name] = new Cookie($name, $value);
        }

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function deleteCookie($name)
    {
        unset ($this->cookies[$name]);

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function clearCookies()
    {
        $this->cookies = new CookiesBag();

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
     * Set XML data
     *
     * @param string|DOMDocument $xmlData
     *
     * @return Request
     */
    public function setXmlData($xmlData)
    {
        if (is_string($xmlData)) {
            $domDocument = new \DOMDocument;

            // Load xml data without warnings
            if(!@$domDocument->loadXML(trim($xmlData))) {
                throw new \RuntimeException('Can\'t load xml data to DOMDocument object.');
            }

            $this->xml_data = $domDocument;
        }
        else if ($xmlData instanceof \DOMDocument) {
            $this->xml_data = $xmlData;
        }
        else {
            throw new \RuntimeException('XML data must be a string or DOMDocument object.');
        }

        return $this;
    }

    /**
     * Get XML data
     *
     * @return \DOMDocument
     */
    public function getXmlData()
    {
        return $this->xml_data;
    }

    /**
     * Get XML Data as string
     *
     * @return string
     */
    public function getXmlDataString()
    {
        if (!$this->xml_data) {
            throw new \LogicException('Can\'t get XML data as string. XML data is empty.');
        }

        return $this->xml_data->saveXML();
    }

    /**
     * Validate XML data and post data
     *
     * @throws \LogicException
     */
    public function validateXmlPostData()
    {
        if ($this->post_data && $this->xml_data) {
            throw new \LogicException('Can\'t use post data and xml data in request.');
        }
    }

    /**
     * @{inerhitDoc}
     */
    public function setHeaders($headers)
    {
        if (!is_array($headers) && !$headers instanceof \Iterator && !$headers instanceof CookiesBag) {
            throw new \InvalidArgumentException('Headers must be HeadersBag object or iterable.');
        }

        if ($headers instanceof HeadersBag) {
            $this->headers = $headers;
            return $this;
        }

        $this->headers = new HeadersBag();

        foreach ($headers as $headerName => $headerValue) {
            $this->addHeader($headerName, $headerValue);
        }

        return $this;
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
     * @{inerhitDoc}
     */
    public function setUri($uri)
    {
        if ($uri instanceof Uri) {
            $this->uri = $uri;
        }
        else {
            $this->uri = Uri::parseFromString($uri);
        }

        return $this;
    }

    /**
     * Set HTTP Version
     *
     * @param string $version
     */
    public function setHttpVersion($version)
    {
        $this->httpVersion = $version;

        return $this;
    }

    /**
     * Get HTTP Version
     *
     * @return float
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }

    /**
     * Set referer
     *
     * @param string $referer
     */
    public function setReferer($referer)
    {
        if ($referer instanceof Uri) {
            $this->headers['Referer'] = $referer;
        }
        else {
            $this->headers['Referer'] = Uri::parseFromString($referer);
        }

        return $this;
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
        $this->redirectUseCookie = (bool) $status;
        return $this;
    }

    /**
     * Generate boundary for POST
     */
    protected function getPostBoundary($reset = FALSE)
    {
        if ($this->_boundary && !$reset) {
            return $this->_boundary;
        }

        // TODO: Generate boundary
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
            // Use link to headers var, becouse addHeader have recursion to setUserAgent
            $this->headers['User-Agent'] = $this->getUserAgent();
        }

        // If using XML data
        if ($this->xml_data) {
            $this->addHeader('Content-Type', 'text/xml');
            $this->setMethod('POST');
        }

        // If using post data
        if ($this->post_data) {
            $this->setMethod('POST');
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
            $this->countUseRedirect = 0;
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
        if ($this->count_redirect && $this->countUseRedirect >= $this->count_redirect) {
            throw new RedirectException(sprintf('Many redirect: <b>%s</b>', $this->countUseRedirect));
        }

        $this->countUseRedirect++;

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
        if ($this->redirectUseCookie) {
            $setCookies = new CookieFilter($this->result->getCookies());

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
    protected function generateResult($content)
    {
        $this->result = new Result;
        $this->result->parsePageContent($content);
    }
}