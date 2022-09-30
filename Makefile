lint:
	vendor/bin/phpstan analyse -c phpstan.neon --memory-limit 1G
	vendor/bin/php-cs-fixer fix

test-behavior:
	php -d memory_limit=4G vendor/bin/behat

test: lint test-behavior

start-php:
	php -S localhost:8080 -t tests/integration/server 2> /dev/null

start-swoole:
	php tests/integration/server/swoole.php
