Web REQUEST (PHP > 5.3)
=======================

Core Concepts
-------------

To use the query you want to use this type of query. For example a socket or stream.
WebAbstratst - is abstract core for sending requests HTTP and HTTPS.


Usage
-----

Basic usage:
Basic http request to domain.

```php
use RequestStream\Request\Web\Socket\Request;

$request = new Request('http://google.com');
$result = $request->getResult();

// Get data
$requestData = $result->getData();
// Get headers
$requestHeaders = $result->getHeaders();
// Get cookies
$requestCookies = $result->getCookies();
```
