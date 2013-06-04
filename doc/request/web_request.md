Web Request
===========

You can send GET, POST or another method request.

Example set request to web connection:
```php
use RequestStream\Request\Web\Socket\Request;
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
use RequestStream\Request\Web\Socket\Request;
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