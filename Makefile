check: php-cs-fixer phpstan deptrac phpunit

CONTAINER=docker-compose exec workshop_app

php-cs-fixer:
	${CONTAINER} ./vendor/bin/php-cs-fixer fix -vv

phpstan:
	${CONTAINER} ./vendor/bin/phpstan analyse src tests -l 7

deptrac:
	${CONTAINER} ./vendor/bin/deptrac

phpunit:
	${CONTAINER} ./vendor/bin/phpunit

phpunit-coverage:
	${CONTAINER} ./vendor/bin/phpunit --coverage-text
