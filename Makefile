uid = $$(id -u)
gid = $$(id -g)
pwd = $$(pwd)

cli:
	docker-compose run php ash

test:
	docker-compose run php ash -c "composer run test"

check-style:
	docker-compose run php ash -c "composer run ecs-check"

fix-style:
	docker-compose run php ash -c "composer run ecs-fix"

psalm:
	docker-compose run php ash -c "composer run psalm"

.PHONY: cli test
