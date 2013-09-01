Web Request
===========

You can send GET, POST or another method request.

Example set request to web connection:

```php
use RequestStream\Request\Web\Socket\Connection;
use RequestStream\Request\Web\DefaultRequest;

// Create connection
$connection = new Request;

// Create request
$defaultRequest = new DefaultRequest();
$defaultRequest
    ->setUri('http://google.com');

// Set request to connection
$connection->setRequest($defaultRequest);

$result = $connection->getResult();

$data = $result->getData();
```

You can manipulate headers bag and cookies bag only from request object:

```php
// Set headers
$defaultRequest
    ->getHeaders()
    ->add('referer', 'http://domain.com')
    ->add('my_header', 'foo, bar');

// Set cookies
$defaultRequest
    ->getCookies()
    ->add('cookie1', 'value1')
    ->add('cookie2', 'value2');
```

Set HTTP Parameters (method, version):

```
$defaultRequest
    ->setMethod('POST')
    ->setHttpVersion('1.1');
```

POST Request
============

```php
use RequestStream\Request\Web\Socket\Connection;
use RequestStream\Request\Web\PostRequest;

$connection = new Request;

// Create post request
$postRequest = new PostRequest();
$postRequest
    ->setUri('http://google.com');

// Get post data bag and set items
$postRequest
    ->getPostData()
        ->add('post_data1', 'value1')
        ->add('post_data2', 'value2')
        ->add('post_data3[name]', 'value3');


$connection->setRequest($postRequest);
```

Custom RAW Data
===============

You can send custom raw data:

Default available:
* native - Base text/plain
* xml - XML document
* json

Base examples:

```php
$request = new DefaultRequest();
$request->setContentData('My custom content data.'); // Use native compiler

$request->setContentData(array('var1' => 'value2')); // Use JSON compiler

$domDocument = new \DOMDocument();
$domDocument->loadXML('<root><book id="1" /></root>');
$request->setContentData($domDocument); // Use XML Compiler
```

> As default you can't set content data to post request, because post request are used own data.
If you want set force custom raw data to post request, please use `DefaultRequest` and set POST method.

Example:

```php
$request = new DefaultRequest();
$request->setContentData('Content data with POST request');
$request->setMethod('POST');
```

### Create custom compiler

You can create custom compiler for compile raw data.

```php
use RequestStream\Request\Web\ContentDataCompiler\CompilerInterface;
use RequestStream\Request\Web\ContentDataCompiler\CompilerFactory;

class MyCustomCompiler implements CompilerInterface
{
    public function compile($data)
    {
        return mb_strlen($data) . ':' . $data;
    }
}

CompilerFactory::add('my_compiler', new MyCustomCompiler());

$request->addContentData('My data', 'my_compiler');

```