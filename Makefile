
# Variables
DOCKER_COMPOSE = docker compose
PHP_CONTAINER = php

# Colors
GREEN = \033[0;32m
NC = \033[0m # No Color

.PHONY: help start stop restart setup shell db-create db-migrate db-fixtures test

help: ## Show this help message
	@echo "Usage: make [command]"
	@echo ""
	@echo "Commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  ${GREEN}%-20s${NC} %s\n", $$1, $$2}'

start: ## Start the Docker stack
	$(DOCKER_COMPOSE) up -d
	@echo "${GREEN}Stack started!${NC}"
	@echo "App available at: https://localhost"

stop: ## Stop the Docker stack
	$(DOCKER_COMPOSE) stop
	@echo "${GREEN}Stack stopped.${NC}"

restart: stop start ## Restart the Docker stack

setup: start ## Setup the project (database, schema, fixtures)
	@echo "Waiting for database to be ready..."
	@powershell -NoProfile -Command "Start-Sleep -Seconds 5"
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) composer install --no-interaction --prefer-dist
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:database:create --if-not-exists
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:migrations:migrate --no-interaction
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction
	@echo "${GREEN}Setup complete!${NC}"

shell: ## Open a shell in the PHP container
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) bash

db-create: ## Create the database
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:database:create

db-migrate: ## Run migrations (or schema update)
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:schema:update --force

db-fixtures: ## Load fixtures
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/console doctrine:fixtures:load --no-interaction

test: ## Run PHPUnit tests
	$(DOCKER_COMPOSE) exec $(PHP_CONTAINER) php bin/phpunit --testdox
