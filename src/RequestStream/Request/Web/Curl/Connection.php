<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Request\Web\Curl;
use RequestStream\Request\Web\ContentDataCompiler\CompilerFactory;
use RequestStream\Request\Web\CookiesBag;
use RequestStream\Request\Web\HeadersBag;
use RequestStream\Request\Web\PostRequest;
use RequestStream\Request\Web\Result;
use RequestStream\Request\Web\WebAbstract;

/**
 * CURL Connection
 */
class Connection extends WebAbstract
{
    /**
     * @var resource
     */
    private $curlResource;

    /**
     * Create and sending first request
     */
    protected function createRequest()
    {
        $this->curlResource = curl_init();
        $requestUri = $this->getRequest()->getUri();

        // Get request
        $request = $this->getRequest();

        // Set default parameters
        curl_setopt($this->curlResource, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlResource, CURLOPT_URL, (string) $requestUri);
        curl_setopt($this->curlResource, CURLOPT_FOLLOWLOCATION, false); // Disable auto location.
        curl_setopt($this->curlResource, CURLOPT_HEADER, true); // Set the return original HTTP headers for next parse
        curl_setopt($this->curlResource, CURLOPT_HTTP_VERSION, $request->getHttpVersion());
        curl_setopt($this->curlResource, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($this->curlResource, CURLOPT_HTTPHEADER, $this->getHeadersArray($request->getHeaders()));

        if (count($request->getCookies())) {
            // Cookies exists
            curl_setopt($this->curlResource, CURLOPT_COOKIE, implode('; ', $request->getCookies()->all()));
        }

        if ($request instanceof PostRequest) {
            // Post request. Add post fields
            curl_setopt($this->curlResource, CURLOPT_POST, true);
            curl_setopt($this->curlResource, CURLOPT_POSTFIELDS, $request->getPostData()->getMinimizeString('&'));
        } else if ($request->getContentData()) {
            // Content data already exists. Add as post fields
            $content = CompilerFactory::compile($request->getContentDataCompiler(), $request->getContentData());
            curl_setopt($this->curlResource, CURLOPT_POSTFIELDS, $content);
        }

        // Start usage time
        $useTime = microtime(true);

        return Result::parseFromContent($this->request, curl_exec($this->curlResource), microtime(true) - $useTime);
    }

    /**
     * Generate headers array
     *
     * @param HeadersBag $headers
     * @return array
     */
    private function getHeadersArray(HeadersBag $headers)
    {
        $result = array();

        foreach ($headers as $key => $value) {
            $result[] = $key . ': ' . (string) $value;
        }

        return $result;
    }
}

