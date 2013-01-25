<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\CookiesBag,
    RequestStream\Request\Web\Cookie,
    RequestStream\Request\ParametersBagInterface;

/**
 * Headers bag test
 */
class CookiesBagRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Headers bag test
     */
    public function testCookiesBag()
    {
        $bag = new CookiesBag;

        $bag['c1'] = 'v1';

        $this->assertEquals(count($bag), 1);
        $this->assertTrue($bag['c1'] instanceof Cookie);

        $bag['c2'] = 'v2';
        $this->assertEquals($bag['c2']->getValue(), 'v2');
        $this->assertEquals($bag['c2']->getName(), 'c2');

        $cookie = new Cookie('c3', 'v3');
        $bag[] = $cookie;
        $this->assertEquals($bag['c3']->getName(), 'c3');
        $this->assertEquals($bag['c3']->getValue(), 'v3');

        $this->assertEquals(str_replace(' ', '', (string) $bag), 'c1=v1;c2=v2;c3=v3');
    }
}