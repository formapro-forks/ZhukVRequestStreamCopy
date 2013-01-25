<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\PostData,
    RequestStream\Request\Web\PostDataBag,
    RequestStream\Request\ParametersBagInterface;

/**
 * Cookie tests
 */
class PostDataBagRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base test cookie
     */
    public function testPostDataBag()
    {
        $bag = new PostDataBag;
        $this->assertTrue($bag instanceof ParametersBagInterface);

        $bag['k1'] = 'v1';

        $this->assertEquals(count($bag), 1);

        $this->assertTrue($bag['k1'] instanceof PostData);

        $this->assertEquals($bag['k1']->getName(), 'k1');
        $this->assertEquals($bag['k1']->getValue(), 'v1');

        $refProperty = new \ReflectionProperty($bag, 'boundary');
        $refProperty->setAccessible(TRUE);
        $refProperty->setValue($bag, 'qw123');

        $postDataString = "\r\n--qw123\n" .
            'Content-Disposition: form-data; name="k1"' . "\r\n\r\n" . "v1" . "\r\n" .
            '--qw123--';

        $this->assertEquals((string) $bag, $postDataString);
    }
}