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
 * Interface for control web request
 */
interface WebInterface
{
    /**
     * Set request
     *
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request);

    /**
     * Get request data
     *
     * @return RequestInterface
     */
    public function getRequest();

    /**
     * Set max count redirect
     *
     * @param int $count
     */
    public function setCountRedirect($count = 0);

    /**
     * Set using cookie in redirected
     *
     * @param bool $status
     */
    public function setRedirectCookie($status = true);

    /**
     * Send request
     *
     * @param bool $reset
     *
     * @return Result
     */
    public function sendRequest($reset = false);

    /**
     * Get result request
     *
     * @param bool $reset
     *
     * @return Result
     */
    public function getResult($reset = false);
}
