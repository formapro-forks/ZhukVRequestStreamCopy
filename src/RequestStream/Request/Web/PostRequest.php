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
 * Post request
 */
class PostRequest extends DefaultRequest
{
    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * @var PostDataBag
     */
    protected $postData;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->postData = new PostDataBag;
    }

    /**
     * Set post data
     *
     * @param PostDataBag|array|\Traversable $postData
     * @throws \InvalidArgumentException
     * @return PostRequest
     */
    public function setPostData($postData)
    {
        if (!$postData instanceof PostDataBag && !is_array($postData) && !$postData instanceof \Traversable) {
            throw new \InvalidArgumentException(sprintf(
                'Post data must be array or PostDataBag instance, "%s" given.',
                is_object($postData) ? get_class($postData) : gettype($postData)
            ));
        }

        if ($postData instanceof PostDataBag) {
            $this->postData = $postData;
        } else {
            $this->postData = new PostDataBag($postData);
        }

        return $this;
    }

    /**
     * Get post data
     *
     * @return PostDataBag
     */
    public function getPostData()
    {
        return $this->postData;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethod($method)
    {
        throw new \BadMethodCallException(sprintf(
            'Can\'t set HTTP method ("%s") to POST Request.',
            $method
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function setContentData($contentData, $compiler = null)
    {
        throw new \BadMethodCallException('Can\'t set content data to post request.');
    }

    /**
     * {@inheritDoc}
     */
    public function setContentDataCompiler($compiler)
    {
        throw new \BadMethodCallException('Can\'t set content data compiler to post request. Content data is disabled in this request.');
    }

    /**
     * {@inheritDoc}
     */
    public function setAutoContentType($status)
    {
        throw new \BadMethodCallException('Can\'t set auto generated content data status to post request. Content data is disabled in this request.');
    }

    /**
     * {@inheritDoc}
     */
    public function prepare()
    {
        parent::prepare();
        $this->headers['Content-Type'] = 'multipart/form-data; boundary="' . $this->postData->generateBoundary() . '"';
        $this->headers['Content-Length'] = $this->postData->getContentLength();
    }

    /**
     * __toString
     */
    public function __toString()
    {
        return rtrim(parent::__toString()) .
            "\r\n" . ((string) $this->postData) . "\r\n\r\n";
    }
}