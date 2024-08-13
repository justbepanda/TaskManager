install:
	composer install
test:
	composer exec --verbose phpunit tests

lint:
	composer exec --verbose phpcs -- --standard=phpcs.xml app public tests
	composer exec --verbose phpstan -- analyse -c phpstan.neon

lint-fix:
	composer exec --verbose phpcbf -- --standard=phpcs.xml app public tests

test-coverage:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml

test-coverage-text:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-text

test-coverage-html:
	XDEBUG_MODE=coverage composer exec --verbose phpunit tests -- --coverage-html coverage
