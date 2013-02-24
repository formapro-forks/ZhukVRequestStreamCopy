Abstract socket core
====================

`RequestStream\Stream\Socket\Socket` - abstract socket core, for manipulation
`SocketClient` and `SocketServer` connection.

[Documentation in php.net](http://www.php.net/manual/ref.stream.php)

Abstract core has methods:

* setContext - set context for creating socket connection
* getContext - get sets context
* setTransport - set connection transport (Example: `tcp`)
* getTranport - get connection transport
* setTarget - set target connection
* getTarget - get target connection
* setPort - set port connection
* getPort - get port connection
* getRemoteSocket - get full target for create socket connection (Example: `tcp://127.0.0.1:1111`)
* is - is socket connection started
* close - close connection
* [shutdown](http://www.php.net/manual/en/function.stream-socket-shutdown.php) - shutdown connection
* [setBlocking](http://www.php.net/manual/en/function.stream-set-blocking.php)
* [setTimeout](http://www.php.net/manual/en/function.stream-set-timeout.php)
* [selectRead](http://www.php.net/manual/en/function.stream-select.php) - only select read
* [selectWrite](http://www.php.net/manual/en/function.stream-select.php) - only write read
* [selectExpect](http://www.php.net/manual/en/function.stream-select.php) - only expect read
* [write](http://www.php.net/manual/en/function.fwrite.php) - write to conenction
* [read](http://www.php.net/manual/en/function.stream-get-contents.php) - read from socket

This core extends from [`RequestStream\Stream\StreamAbstract`](../stream.md)

## Examples (via SocketClient):

### Get remote socket:
```php
$socket->setTransport('tcp');
$socket->setTarget('google.com');
$socket->setPort(80);
var_dump($socket->getRemoteSocket());
```

**Attention**
> Can't set transport, target and port if connection already created!

### Is create connection:
```php
var_dump($socket->is()); // false
$socket->create();
var_dump($socket->is()); // true
```

### Close connection:
```php
$socket->create();
var_dump($socket->is()); // true
$socket->close();
var_dump($socket->is()); // False
```

### Shutdown connection:
```php
$socket->shutdown(); // Default: STREAM_SHUT_RDWR
$socket->shutdown(STREAM_SHUT_RD); // disable further receptions
$socket->shutdown(STREAM_SHUT_WR); // disable further transmissions
$socket->shutdown(STREAM_SHUT_RDWR); // disable further receptions and transmissions
```

### Write/Read:
```php
$socket->write('Write content...');
$content = $socket->read();
```

## Next steps:

* [Socket client](socket_client.md)

[Socket issue tracker](https://github.com/ZhukV/RequestStream/issues?labels=socket)