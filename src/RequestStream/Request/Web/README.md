Web REQUEST (PHP > 5.3)
=======================

Core Concepts
-------------

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
