<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Stream\Context\Context,
    RequestStream\Stream\Context\ContextInterface,
    RequestStream\Stream\StreamInterface;

/**
 * Context test
 */
class ContextTest extends \PHPUnit_Framework_TestCase
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
            }
            else {
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

        $this->assertTrue($context instanceof StreamInterface);
        $this->assertTrue($context instanceof ContextInterface);

        // Test started
        $this->assertFalse($context->is(FALSE));

        // Create context
        $context->create();

        $this->assertTrue(is_resource($context->getResource()));
        $this->assertTrue(is_array($context->getOptions()));
        $this->assertTrue($context->is(FALSE));
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
            'request_fulluri' => array(TRUE, FALSE),
            'follow_location' => array(2, 5),
            'max_redirects' => array(2, 5),
            'protocol_version' => array('1.0', '1.1'),
            'ignore_errors' => array(TRUE, FALSE)
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

            $this->assertEquals($setsHttpOptions[$k], $v);
        }
    }

}