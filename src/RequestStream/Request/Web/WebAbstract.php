<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web;

use RequestStream\Request\Exception\RequestException;
use RequestStream\Request\Exception\ResultException;
use RequestStream\Request\Exception\RedirectException;
use RequestStream\Request\Web\Result;

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
     * @var integer
     */
    protected $countUseRedirect = 0;

    /**
     * @var boolean
     */
    protected $redirectUseCookie = true;

    /**
     * Construct
     *
     * @param string|Uri|RequestInterface $uri
     */
    public function __construct($uri = null)
    {
        if ($uri instanceof RequestInterface) {
            // Request instance
            $this->request = $uri;
        } else {
            $this->request = new DefaultRequest();

            if ($uri) {
                $this->request->setUri($uri instanceof Uri ? $uri : Uri::parseFromString($uri));
            }
        }
    }

    /**
     * Create a new instance
     *
     * @param string|Uri $uri
     */
    public static function newInstance($uri = null)
    {
        return new static($uri);
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritDoc}
     */
    public function setCountRedirect($count = 0)
    {
        $this->countRedirect = (int) $count;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setRedirectCookie($status = true)
    {
        $this->redirectUseCookie = (bool) $status;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getResult($reset = false)
    {
        return $this->sendRequest($reset);
    }

    /**
     * {@inheritDoc}
     */
    public function sendRequest($reset = false)
    {
        if (!$this->request) {
            throw new RequestException('Can\'t send request to remote address. Undefined request data.');
        }

        if (!$this->request->getUri()) {
            throw new RequestException('Can\'t send request to remove address. Undefined request URI.');
        }

        if ($this->result && !$reset) {
            return $this->result;
        }

        // Reset count use redirect
        if (!$reset) {
            $this->countUseRedirect = 0;
        }

        /** @var Result $webResult */
        $webResult = $this->createRequest();

        if (!$webResult instanceof ResultInterface) {
            $this->result = null;
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

        $refererUri = (string) $this->request->getUri();

        $this->request->getHeaders()->add('Referer', $refererUri);

        // Generate and set location
        $locationTo = $this->result->headers['Location'];

        if (strpos($locationTo, '/') === 0) {
            /** @var Uri $requestUri */
            $requestUri = $this->request->getUri();
            $locationTo = $requestUri->getDomain() . $locationTo;
        } else if (strpos($locationTo, 'http') !== 0) {
            /** @var Uri $requestUri */
            $requestUri = $this->request->getUri();

            $path = explode('/', $requestUri->getPath());
            array_pop($path);

            $locationTo = $requestUri->getDomain() . '/' . ltrim(implode('/', $path), '/') . $locationTo;
        }

        $requestData = new DefaultRequest;
        $requestData->setUri(Uri::parseFromString($locationTo));

        // If used save cookie for redirect
        if ($this->redirectUseCookie) {
            $cookieFilter = new CookieFilter($this->result->getCookies());
            $cookieBag = $this->request->getCookies();

            /** @var Cookie $cookie */
            foreach ($cookieFilter as $cookie) {
                $cookieBag[$cookie->getName()] = $cookie;
            }

            $requestData->setCookies($cookieBag);
        }

        $this->request = $requestData;

        return $this->sendRequest(true);
    }

    /**
     * Create and sending first request
     */
    abstract protected function createRequest();
}