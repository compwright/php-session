# Compwright\PhpSession

A PHP session implementation that is single-process safe, compatible with PSR-7 and PSR-15, and
does not rely on global variables ($_SESSION, $_COOKIE, etc).

This implementation is patterned after the built-in PHP session library, but is not a drop-in
replacement for it. This library differs from the PHP session library in the following ways:

* Requires PHP 7.4+
* Fully object-oriented
* Strict mode is always on and cannot be disabled
* Auto-start and auto-shutdown are not supported
* Reading/writing cookie and cache headers is handled in middleware (included)
* Handlers implement the built-in PHP SessionHandlerInterface, but the PHP SessionHandler class
  will not work because it depends internally on the PHP session extension
* Session data is accessed using a Session object, not via $_SESSION

This library is ideal for single-process event loop-driven applications, using servers like
[Swoole](https://www.swoole.co.uk) or [ReactPHP](https://reactphp.org).

## Supported Features

* [Collision-Proof Secure ID Generation](features/id.feature)
* [Data Persistance](features/persistence.feature)
* [ID Regeneration](features/regeneration.feature)
* [Lockless Concurrency](features/concurrency.feature)
* [Garbage Collection](features/gc.feature)

## Installation

    composer require compwright/php-session

## Examples

### Slim Framework

See [tests/integration/server/App](tests/integration/server/App)

To run with PHP Development Server:

    $ composer run-script start-php

To run with [Swoole](https://www.swoole.co.uk/docs/get-started/installation):

    $ composer run-script start-swoole

### Basic Usage

```php
$sessionFactory = new Compwright\PhpSession\Factory();

$manager = $sessionFactory->psr16Session(
    /**
     * @param Psr\SimpleCache\CacheInterface
     */
    $cache,

    /**
     * @param array|Compwright\PhpSession\Config
     */
    [
        'name' => 'my_app',
        'sid_length' => 48,
        'sid_bits_per_character' => 5,
    ]
);

// Start the session
$manager->id($sid); // Read $sid from request
$started = $manager->start();
if ($started === false) {
    throw new RuntimeException("The session failed to start");
}

// Read/write the current session
$session = $manager->getCurrentSession();
$session["foo"] = "bar";
unset($session["bar"]);

// Save and close the session
$ended = $manager->write_close();
if ($ended === false) {
    throw new RuntimeException("The session failed to close properly, data may have been lost");
}
```

## License

[MIT License](LICENSE)
