.PHONY: up down sh logs install db-reset

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

# Drops the database volume so MySQL re-runs docker/mysql/init/schema.sql.
db-reset:
	docker compose down -v
	$(MAKE) up
