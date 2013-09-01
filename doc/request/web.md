Web Connection
==============

You can send seb request via Socket Connection.

### Step 1 (Create connection)

```php
use RequestStream\Request\Web\Socket\Connection;
$connection = new Request('http://google.com');
```

### Step 2 (Get result)

```php
$result = $connection->getResult();
```

Web Result
==========

Each result has a data:

* Content data
* Headers bag
* Cookies bag
* Time requested
* Status code
* HTTP Protocol

Example manipulate with result:

```php
// Get result data
$data = $result->getData();

// Get headers
$headers = $result->getHeaders();

// Get cookies
$headers = $result->getCookies();

// Get requested time
$headers = $result->getRequestTime();

// Get status code
$code = $result->getCode();
```

> **Note:** Cookies and headers bag instance \ArrayAccess, and you can manipulate this object as array:

```php
$headers = $result->getHeaders();

print isset($headers['Date']);
// Analog:
print isset($headers['date']);

$cookies = $result->getCookies();

print $cookies['cookieName'];
```

The following documents are available:

- [Request method](web_request.md)