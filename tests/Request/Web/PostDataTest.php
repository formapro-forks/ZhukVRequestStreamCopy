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

use RequestStream\Request\ParametersBagInterface;

/**
 * PostData and PostDataBag tests
 */
class PostDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base test post data
     */
    public function testPostData()
    {
        $postData = new PostData('name', 'value');
        $this->assertEquals($postData->getName(), 'name');
        $this->assertEquals($postData->getValue(), 'value');

        $stringPostData = 'Content-Disposition: form-data; name="' . $postData->getName() . '"' . "\r\n\r\n" . $postData->getValue();

        $this->assertEquals((string) $postData, $stringPostData);
    }

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