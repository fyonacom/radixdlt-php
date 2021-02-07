uid = $$(id -u)
gid = $$(id -g)
pwd = $$(pwd)

cli:
	docker-compose run php ash

test:
	docker-compose run cli ash -c "XDEBUG_MODE=coverage php artisan test"

.PHONY: cli test
