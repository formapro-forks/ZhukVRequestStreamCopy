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
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var DOMDocument
     */
    protected $xmlData;

    /**
     * @var string
     */
    protected $httpVersion = '1.0';

    /**
     * @var Result
     */
    protected $result;

    /**
     * @var integer
     */
    protected $countRedirect = 5;

    /**
     * @var boolean
     */
    protected $redirectUseCookie = TRUE;

    /**
     * Construct
     *
     * @param string|Uri $uri
     */
    public function __construct($uri = NULL)
    {
        $this->request = new DefaultRequest();

        if ($uri) {
            $this->request->setUri($uri instanceof Uri ? $uri : Uri::parseFromString($uri));
        }
    }

    /**
     * @{inerhitDoc}
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @{inerhitDoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @{inerhitDoc}
     */
    public function setXmlData($xmlData)
    {
        if (count($this->postData)) {
            throw new \LogicException('Can\'t set XML Data, because already exists POST Data. Please clear post data.');
        }

        if (is_string($xmlData)) {
            $domDocument = new \DOMDocument;

            // Load xml data without warnings
            if(!@$domDocument->loadXML(trim($xmlData))) {
                throw new \RuntimeException('Can\'t load xml data to DOMDocument object.');
            }

            $this->xmlData = $domDocument;
        }
        else if ($xmlData instanceof \DOMDocument) {
            $this->xmlData = $xmlData;
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
        return $this->xmlData;
    }

    /**
     * Set count redirect
     *
     * @param integer $count
     */
    public function setCountRedirect($count = 0)
    {
        $this->countRedirect = (int) $count;

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
     * Prepare headers
     */
    protected function prepareHeaders()
    {
        throw new \RuntimeException('DEPRECATED');

        // If using XML data
        if ($this->xmlData) {
            if (!$this->headers->has('Content-Type')) {
                $this->headers['Content-Type'] = 'application/xml';
            }

            $this->setMethod('POST');
        }
    }

    /**
     * @{inerhitDoc}
     */
    public function getResult($reset = FALSE)
    {
        return $this->sendRequest($reset);
    }

    /**
     * @{inerhitDoc}
     */
    public function sendRequest($reset = FALSE)
    {
        if (!$this->request) {
            throw new RequestException('Can\'t send request to remote address. Undefined request data.');
        }

        if ($this->result && !$reset) {
            return $this->result;
        }

        // Reset count use redirect
        if (!$reset) {
            $this->countUseRedirect = 0;
        }

        // Create request
        $webResult = $this->createRequest();

        if (!$webResult instanceof ResultInterface) {
            $this->result = NULL;
            throw new ResultException('Can\'t get result. Result must be instance of ResultInterface.');
        }

        $this->result = $webResult;

        // If moved (Location)
        if (in_array($webResult->getCode(), array(301, 302, 307)) &&
            $webResult->headers->has('Location')
        ) {
            return $this->sendRequestRedirect();
        }

        return $this->result;
    }

    /**
     * Create request redirect
     */
    protected function sendRequestRedirect()
    {
        // Static variable for control count redirects
        static $countRedirects = 0;

        // Check count redirects
        if ($countRedirects && $countRedirects >= $this->countRedirect) {
            $useCountRedirects = $countRedirects;
            $countRedirects = 0;
            throw new RedirectException(sprintf('Many redirect: "%s"', $useCountRedirects));
        }

        $countRedirects++;

        $this->heders['Referer'] = $refererUri = $this->request->getUri();

        // Generate and set location
        $locationTo = $this->result->headers['Location'];

        if (strpos($locationTo, '/') === 0) {
            $locationTo = $this->uri->getDomain() . $locationTo;
        }
        else if (strpos($locationTo, 'http') === FALSE) {
            $locationTo = $this->uri->getDomain() .
                rtrim($this->uri->getPath(), '/') .
                '/' . $locationTo;
        }

        $requestData = new DefaultRequest;
        $requestData->setUri(Uri::parseFromString($locationTo));

        // If used save cookie for redirect
        if ($this->redirectUseCookie) {
            $cookieFilter = new CookieFilter($this->result->getCookies());
            $cookieBag = $this->request->getCookies();

            foreach ($cookieFilter as $cookie) {
                $cookieBag[$cookie->getName()] = $cookie;
            }

            $requestData->setCookies($cookieBag);
        }

        $this->request = $requestData;

        return $this->sendRequest(TRUE);
    }


    /**
     * Create and sending first request
     */
    abstract protected function createRequest();
}