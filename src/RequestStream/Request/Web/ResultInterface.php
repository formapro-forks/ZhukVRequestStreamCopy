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

/**
 * Interface for control result
 */
interface ResultInterface
{
    /**
     * Get request
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Get result data
     *
     * @return string
     */
    public function getData();

    /**
     * Get headers bag
     *
     * @return HeadersBag
     */
    public function getHeaders();

    /**
     * Get code status (Status server)
     *
     * @return int
     */
    public function getCode();

    /**
     * Get protocol
     *
     * @return string
     */
    public function getProtocol();

    /**
     * Get cookies bag
     *
     * @return CookiesBag
     */
    public function getCookies();

    /**
     * Get request time
     *
     * @return float
     */
    public function getRequestTime();

    /**
     * Parse page content
     *
     * @param string $content
     * @param float $useTime
     * @return ResultInterface
     */
    public static function parseFromContent(RequestInterface $request, $content, $useTime = null);
}