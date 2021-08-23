# Compwright\PhpSession

A PHP session implementation that is single-process safe, compatible with PSR-7 and PSR-15, and
does not rely on global variables ($_SESSION, $_COOKIE, etc).

This implementation is patterned after the built-in PHP session library, but is not a drop-in
replacement for it, because $_SESSION global dependence is a fundamental component.

This library differs from the PHP session library in the following ways:

* Requires PHP 7.4+
* Fully object-oriented approach
* Strict mode is always on and cannot be disabled
* Reading/writing cookie and cache headers is handled outside the main library, in middleware

However, this library is compatible in the following ways:

* Standard PHP session configuration options can be read
* Standard PHP save handlers should just work
* Standard PHP serialization settings should just work

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
// Read session.* INI settings
$configFactory = new Compwright\PhpSession\ConfigFactory();
$config = $configFactory->createFromSystemConfig();

// Start the session
$manager = new Compwright\PhpSession\Manager($config);
$manager->id($sid); // Read $sid from request
$started = $manager->start();
if ($started === false) {
    throw new RuntimeException("The session failed to start");
}

// Get the current session
$session = $manager->getCurrentSession();

// Write to the session
$session["foo"] = "bar";

// Remove data from the session
unset($session["bar"]);

// Save and close the session
$ended = $manager->write_close();
if ($ended === false) {
    throw new RuntimeException("The session failed to close properly, data may have been lost");
}
```

## License

[MIT License](LICENSE)
