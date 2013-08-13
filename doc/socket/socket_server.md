Socket server
=============

You can create socket server for connection to this server from another system/servers.

[Documentation in php.net](http://www.php.net/manual/ref.stream.php)

Available transports:
* TCP
* UPD

Default create socket server:

```php
use RequestStream\Stream\Socket\SocketServer;

$server = new SocketServer;
$server
    ->setTarget('localhost') // Listen on host
    ->setPort('1234') // Listen on port
    ->setTransport('tcp'); // Listen on transport (UPD or TCP)

// Get a original resource
$resource = $server->create();
```

And connection to this socket via telnet from localhost:

```bash
$ telnel localhost 1234
# Or another IP or host
$ telnet domain.com 1234
```

Accept command
--------------

But after connection to socket, socket will closed, because you must create own command for
 control each input connection.

Default example:

```php
use RequestStream\Stream\Socket\SocketServer;
use RequestStream\Stream\Socket\Server\AcceptCommand;
use RequestStream\Stream\Socket\Server\ConnectionInterface;

// Create own accept command
class MyCommand extends AcceptCommand
{
    /**
     * {@inheritDoc}
     */
    public function execute(ConnectionInterface $connection)
    {
        $connection->writeln("Hello friend. This is a remote socket");
    }
}

// And set this command to socket server and run accept
$server = new SocketServer;
$server
    ->setTarget('localhost')
    ->setPort('1234')
    ->setTransport('tcp');

$resource = $server->create();
$server->setAcceptCommand(new MyCommand());
$server->acceptRun();
```

Output via telnet:
```
$ telnet localhost 1234
Trying ::1...
Connected to localhost.
Escape character is '^]'.
Hello friend. This is a remote socket
Connection closed by foreign host.
$
```