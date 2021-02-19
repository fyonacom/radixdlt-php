uid = $$(id -u)
gid = $$(id -g)
pwd = $$(pwd)

cli:
	docker-compose run php ash

test:
	docker-compose run php ash -c "XDEBUG_MODE=coverage ./vendor/bin/phpunit --coverage-html coverage --testdox"

check-style:
	docker-compose run php ash -c "php /srv/vendor/bin/ecs check"

fix-style:
	docker-compose run php ash -c "php /srv/vendor/bin/ecs check --fix"

psalm:
	docker-compose run php ash -c "php /srv/vendor/bin/psalm"

.PHONY: cli test
