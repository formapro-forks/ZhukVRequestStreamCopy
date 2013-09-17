<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\ParametersBag;

/**
 * Parameters bag test
 */
class ParametersBagRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Parameters bag test
     */
    public function testParametersBag()
    {
        $bag = new ParametersBag;
        $bag['k1'] = 'v1';
        $this->assertCount(1, $bag);
        $this->assertEquals($bag['k1'], 'v1');
        unset ($bag['k1']);
        $this->assertCount(0, $bag);
    }
}