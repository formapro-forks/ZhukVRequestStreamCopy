RequestStream (Stream)
======================

You can create stream connection with use this component.

Stream allowed:

* Context
* Socket (Client and server)

[Documentation in php.net](http://www.php.net/manual/ref.stream.php)

## Core concept:

Each element of stream must be instance `StreamAbstract` class.

## StreamAbstract

This core has methods for control streams in your system:

* isWrapper (Check wrapper by name)
* getWrappers (Get all allowed wrappers)
* isTransport (Check transport by name)
* getTransports (Get all allowed transports)
* resolveIncludePath
* select (@analog: [stream_select](http://www.php.net/manual/function.stream-select.php))
* getResource (Get original resource, stream, context, etc...)
* is (Is started context)