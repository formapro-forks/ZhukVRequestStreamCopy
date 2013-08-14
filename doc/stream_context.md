RequestStream (Context)
=======================

You can create context with use OOP PHP >= 5.3 language

Context class: `RequestStream\Stream\Context`

**Documentation on php.net:**
[Context](http://php.net/manual/en/context.php)
[Context stream functions](http://www.php.net/manual/en/ref.stream.php)

Context object instance of [AbstractStream](stream.md).

### Working with context:

* Create context:

```php
use RequestStream\Stream\Context;
$context = new Context;
```

**Attention:**
> This code not has create a new resource!

For create new resource in your system and get this resource, please usage:

```php
use RequestStream\Stream\Context;
$context = new Context;
$contextResource = $context->getResource();
```

You can set options to context or get defaults...

Example get default context from system:

```php
use RequestStream\Stream\Context;
$options = array();
$context = Context::getDefault($options);
// or
$context = Context::getDefault();
```

$options is optional parameter.

Example get options from context:

```php
use RequestStream\Stream\Context;
$context = new Context;
$contextOptions = $context->getOptions();
var_dump($contextOptions);
```

And you can get parameter from context with method `getParams` or set parameters to context with method `setParams`

#### Context wrapper options:

Example add wrapper options to context:

```php
use RequestStream\Stream\Context;
$context = new Context;
$context->setOptions('http', 'method', 'POST');
// Or
$context->setOptions('http', array('method' => 'POST'));
// Or
$context->setOptions(array(
    'http' => array(
        'method' => 'POST'
    )
));
```

Context has wrappers for next control:

* http
* ftp
* ssl
* curl
* phar
* socket
* etc...

For more information about wrapper, please see [documentation on php.net](http://php.net/manual/en/wrappers.php)

#### Base example:

```php
// Create new context object
$context = new Context;

// Check started context as resource object
var_dump($context->is()); // false (Context not create as resource)

// Create context as resource
$context->create();

// Check started
var_dump($context->is()); // true (Context created...)

// Add options to context
$context->setOptions('http', 'method', 'POST');

// Get resource
$resource = $context->getResource();
```

**Attention:**

> Method `getResource`, `setOptions`, `setParameter` - auto created context resource, if resource not found.

Example:

```php
// Create new context object
$context = new Context;
$context->getResource();
var_dump($context->is()); // true, because context auto created
```
