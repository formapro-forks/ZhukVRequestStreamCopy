Getting Started with RequestStream
==================================

## Prerequisites

This version of package requires PHP >= 5.3.3

## Installation

### Dowload RequestStream using composer

```js
{
    "require": {
        "request-stream/request-stream": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

```bash
$ php composer.phar update request-stream/request-stream
```

Composer will install the bundle to your project's `vendor/request-stream` directory.

## Next steps:

You can create socket client and web request client with this package.

The following documents are available:

- [Default stream](stream.md)
- [Stream context](stream_context.md)
- [Sockets](socket/socket.md)
- [Socket client](socket/socket_client.md)
- [Socket server](socket/socket_server.md)
- [Web request](request/web.md)
