<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace RequestStream\Stream;

/**
 * Context test
 */
class StreamContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Create a new context
     */
    protected function createContext()
    {
        return new Context;
    }

    /**
     * Set options to context
     *
     * @param string $wrapper
     * @param array $options
     * @param ContextInterface $context
     */
    protected function setOptionsToContext($wrapper, array $options, ContextInterface $context)
    {
        foreach ($options as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k1 => $v1) {
                    $context->setOptions($wrapper, $k, $v1);
                }
            } else {
                $context->setOptions($wrapper, $k, $v);
            }
        }
    }

    /**
     * Base text context
     */
    public function testContext()
    {
        $context = $this->createContext();

        $this->assertTrue(is_resource($context->getDefault()));

        $this->assertInstanceOf('RequestStream\Stream\StreamInterface', $context);

        // Test started
        $this->assertFalse($context->is(false));

        // Create context
        $context->create();

        $this->assertTrue(is_resource($context->getResource()));
        $this->assertTrue(is_array($context->getOptions()));
        $this->assertTrue($context->is(false));
    }

    /**
     * HTTP options context
     */
    public function testHttpOptionsContext()
    {
        $context = $this->createContext();

        $httpOptions = array(
            'method' => array('HEAD', 'GET', 'POST'),
            'header' => array('Generator: RequestStream', 'Cookie: SESS=xxxx'),
            'user_agent' => 'new_user_agent',
            'timeout' => 30,
            'proxy' => 'localhost',
            'request_fulluri' => array(true, false),
            'follow_location' => array(2, 5),
            'max_redirects' => array(2, 5),
            'protocol_version' => array('1.0', '1.1'),
            'ignore_errors' => array(true, false)
        );

        $this->setOptionsToContext('http', $httpOptions, $context);

        // Get all options
        $options = $context->getOptions();

        // Get http options
        $setsHttpOptions = $options['http'];

        foreach ($httpOptions as $k => $v) {
            if (is_array($v)) {
                $v = $v[count($v) - 1];
            }

            $this->assertEquals($v, $setsHttpOptions[$k]);
        }
    }
}