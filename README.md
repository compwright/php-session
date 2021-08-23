# Compwright\PhpSession

A PHP session implementation that is single-process safe, compatible with PSR-7 and PSR-15, and
does not rely on global variables ($_SESSION, $_COOKIE, etc).

This implementation is patterned after the built-in PHP session library, but is not a drop-in
replacement for it, because $_SESSION global dependence is a fundamental component.

This library differs from the PHP session library in the following ways:

* Requires PHP 7.4+
* Fully object-oriented approach
* Strict mode is always on and cannot be disabled
* Auto-start and auto-shutdown are not supported
* Reading/writing cookie and cache headers is handled outside the main library, in middleware
* Only from-scratch session handler implementations can be used, i.e. the built-in PHP
  SessionHandler class cannot be used or extended. Handlers must of course implement the PHP
  SessionHandlerInterface interface.

This library was designed with single-process event loop-driven applications in mind, using
[ReactPHP](https://reactphp.org), [Swoole](https://www.swoole.co.uk), or similar.

## Supported Features

* [Collision-proof secure ID generation](features/id.feature)
* [Data persistance](features/persistance.feature)
* [ID Regeneration](features/regeneration.feature)
* [Session Locking](features/locking.feature)
* [Garbage Collection](features/gc.feature)

## Installation

    composer require compwright/php-session

## Usage Example

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
