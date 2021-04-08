UID := $(shell id -u)

up:
	docker-compose up

shell:
	docker-compose run --rm -u $(UID) app sh

qa:
	docker-compose run --rm -u $(UID) -T app composer qa
