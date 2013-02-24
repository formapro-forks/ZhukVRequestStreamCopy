Socket client
=============

You can create socket client with this package.

[Documentation in php.net](http://www.php.net/manual/ref.stream.php)

Base create socket client:

```php
use RequestStream\Stream\Socket\SocketClient;

$socketClient = new SocketClient;
```

Socket client extends from [`RequestStream\Stream\Socket\Socket`](socket.md).

Your can set flags for open sockets:

* STREAM_CLIENT_CONNECT
* STREAM_CLIENT_ASYNC_CONNECT
* STREAM_CLIENT_PERSISTENT

[Falgs documentation with create socket client](http://www.php.net/manual/function.stream-socket-client.php)

Default create socket client:

```php
// Create HTTP connection to google server
use RequestStream\Stream\Socket\SocketClient;

$socketClient = new SocketClient;
$socketClient->setTransport('tcp');
$socketClient->setTarget('google.com');
$socketClient->setPort(80);
$resource = $socketClient->create();
```

And you can manipulate this socket, writing to socket, read from socket, set blocking options,
set another options, etc...

Default example create web client:
```php
use RequestStream\Stream\Socket\SocketClient;

// Create socket connection to google server
$socketClient = new SocketClient;
$socketClient->setTransport('tcp');
$socketClient->setTarget('google.com');
$socketClient->setPort(80);
$socketClient->create();

// Write headers
$socketClient->write("GET / HTTP/1.0\nHost: google.com");
// Write last caret (HTTP Request Standart)
$socketClient->write("\n\n");

// Read all from socket
$content = $socketClient->read();
print $content;
```
**Attention**
> You can't write and read from socket, if socket connection not created!

[Socket issue tracker](https://github.com/ZhukV/RequestStream/issues?labels=socket+client)

For more information with socket connection, please see
[Socket Documentation](socket.md)