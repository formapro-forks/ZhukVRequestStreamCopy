<?php

/**
 * This file is part of the RequestStream package
 *
 * (c) Vitaliy Zhuk <zhuk2205@gmail.com>
 *
 * For the full copyring and license information, please view the LICENSE
 * file that was distributed with this source code
 */

use RequestStream\Request\Web\PostData;

/**
 * Cookie tests
 */
class PostDataRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Base test cookie
     */
    public function testPostData()
    {
        $postData = new PostData('name', 'value');
        $this->assertEquals($postData->getName(), 'name');
        $this->assertEquals($postData->getValue(), 'value');

        $stringPostData = 'Content-Disposition: form-data; name="' . $postData->getName() . '"' . "\r\n\r\n" . $postData->getValue();

        $this->assertEquals((string) $postData, $stringPostData);
    }
}