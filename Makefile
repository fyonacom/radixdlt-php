uid = $$(id -u)
gid = $$(id -g)
pwd = $$(pwd)

cli:
	docker-compose run php ash

test:
	docker-compose run php ash -c "composer run test"

check-style:
	docker-compose run php ash -c "composer run style-check"

fix-style:
	docker-compose run php ash -c "composer run style-fix"

psalm:
	docker-compose run php ash -c "composer run psalm"

.PHONY: cli test
