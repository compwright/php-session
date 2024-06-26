# Changelog

## [3.2.1](https://github.com/compwright/php-session/compare/v3.2.0...v3.2.1) (2024-01-08)


### Miscellaneous Chores

* **master:** release 3.2.0 ([#19](https://github.com/compwright/php-session/issues/19)) ([2d2c77a](https://github.com/compwright/php-session/commit/2d2c77affe7a64f6c2c5389a345c7dcdde552749))

## [3.2.0](https://github.com/compwright/php-session/compare/v3.1.2...v3.2.0) (2024-01-08)


### Features

* drop Swoole support ([273a6b1](https://github.com/compwright/php-session/commit/273a6b187a4bd19a0459ebbea7a1112482e184cb))


### Bug Fixes

* lint ([ba80392](https://github.com/compwright/php-session/commit/ba8039252bc9e7224cf9293a7b2d1b5ac42ca175))
* overloading behaviour ([#14](https://github.com/compwright/php-session/issues/14)) ([3d43518](https://github.com/compwright/php-session/commit/3d4351824ed014c6eb0057fe1b60c9b6ba9f8cea))


### Miscellaneous Chores

* upgrade deps ([e3082d7](https://github.com/compwright/php-session/commit/e3082d760e7910aa007427c509119ef27681adc5))

## [3.1.2](https://github.com/compwright/php-session/compare/v3.1.1...v3.1.2) (2023-02-14)


### Bug Fixes

* setContents() does not toggle modified flag ([#15](https://github.com/compwright/php-session/issues/15)) ([14395fe](https://github.com/compwright/php-session/commit/14395fe884cf4ae2d5979ecb864de83ed222bff9))

## [3.1.1](https://github.com/compwright/php-session/compare/v3.1.0...v3.1.1) (2022-12-29)


### Bug Fixes

* fix session regeneration when used with a CAS handler ([4adb6a3](https://github.com/compwright/php-session/commit/4adb6a366302212e5415bde4152b67a78c83f076))

## [3.1.0](https://github.com/compwright/php-session/compare/v3.0.1...v3.1.0) (2022-12-28)


### Features

* make Session iterable ([#11](https://github.com/compwright/php-session/issues/11)) ([723a911](https://github.com/compwright/php-session/commit/723a9116e16d1a20373b8d7bcee63c789eede86f))

## [3.0.1](https://github.com/compwright/php-session/compare/v3.0.0...v3.0.1) (2022-12-23)


### Miscellaneous Chores

* fix type checks ([314e8ff](https://github.com/compwright/php-session/commit/314e8ff682484819e10e0cf0c63f4d1fb050617a))
* test in ci ([0cf510a](https://github.com/compwright/php-session/commit/0cf510a9fab5899e2bef2c94b6d5207d517ae932))
* upgrade dev dependencies ([53f29f8](https://github.com/compwright/php-session/commit/53f29f8b3d3a97ee4f8a8a7d6c1df17e1458dfe6))

## [3.0.0](https://github.com/compwright/php-session/compare/v2.0.0...v3.0.0) (2022-12-23)


### ⚠ BREAKING CHANGES

* drop support for PHP 7.4
* support psr/simple-cache v2 and v3

### Features

* drop support for PHP 7.4 ([19358d0](https://github.com/compwright/php-session/commit/19358d039685beca8c8ec14e8cba260aeacdc0fa))
* support psr/simple-cache v2 and v3 ([c3e7563](https://github.com/compwright/php-session/commit/c3e756337fe2de35270201cf9a9271d42bc3b4ee)), closes [#2](https://github.com/compwright/php-session/issues/2)
* upgrade dependencies ([243d859](https://github.com/compwright/php-session/commit/243d859028fdfa0f4be4c8761f63a364b0f0e7f2)), closes [#2](https://github.com/compwright/php-session/issues/2)


### Bug Fixes

* add ArrayAccess to Session class ([#7](https://github.com/compwright/php-session/issues/7)) ([7d13b9d](https://github.com/compwright/php-session/commit/7d13b9dd1fea5243f382ad51802146e5d60c963e))
* Session::__get() should trigger error when data does not exist ([80fea20](https://github.com/compwright/php-session/commit/80fea2000d4d4bb624c8e3fecc196a6ba4697899))
* sid generation in cookie middleware ([#5](https://github.com/compwright/php-session/issues/5)) ([0fe1c63](https://github.com/compwright/php-session/commit/0fe1c6322a46acf0b2ce9e4e7072e80563e28279))
