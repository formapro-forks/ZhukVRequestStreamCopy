<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\ParametersBag,
    RequestStream\Request\ParametersBagInterface;

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
        $this->assertTrue($bag instanceof ParametersBagInterface);

        $bag['k1'] = 'v1';

        $this->assertEquals(count($bag), 1);
        $this->assertEquals($bag['k1'], 'v1');
        unset ($bag['k1']);
        $this->assertEquals(count($bag), 0);
    }
}