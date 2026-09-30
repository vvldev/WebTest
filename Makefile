.PHONY: up down sh logs install

up:
	docker compose up -d --build

down:
	docker compose down

sh:
	docker compose exec php sh

logs:
	docker compose logs -f

install:
	docker compose exec php composer install
