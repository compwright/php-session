lint:
	vendor/bin/phpstan --memory-limit=1G || true
	vendor/bin/phpcbf -q --standard=PSR12 src tests || true
	vendor/bin/phpcs --standard=PSR12 -s src tests

test: lint
	php -d memory_limit=4G vendor/bin/behat

start-php:
	php -S localhost:8080 -t tests/integration/server 2> /dev/null

start-swoole:
	php tests/integration/server/swoole.php
