.PHONY: help up down restart logs db-shell db-reset test test-unit test-integration serve install db-test-up db-test-down

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-15s\033[0m %s\n", $$1, $$2}'

up: ## Start server database
	docker compose up -d db
	@echo "Waiting for database to be ready..."
	@sleep 2
	@docker compose exec db pg_isready -U postgres -d tracknote > /dev/null 2>&1 && echo "Server database is ready!" || echo "Database starting..."

down: ## Stop all containers
	docker compose down

restart: ## Restart server database
	docker compose restart db

logs: ## Show server database logs
	docker compose logs -f db

db-shell: ## Open psql shell (server database)
	docker compose exec db psql -U postgres -d tracknote

db-reset: ## Reset server database (drop and recreate)
	docker compose down -v
	docker compose up -d db
	@echo "Waiting for database to be ready..."
	@sleep 3
	@echo "Database reset complete!"

db-test-up: ## Start test database
	@docker compose up -d db_test
	@echo "Waiting for test database to be ready..."
	@sleep 2
	@docker compose exec db_test pg_isready -U postgres -d tracknote_test > /dev/null 2>&1 && echo "Test database is ready!" || echo "Test database starting..."

db-test-down: ## Remove test database container
	docker compose rm -sf db_test

test: db-test-up ## Run all tests (starts test database automatically)
	@./vendor/bin/phpunit; status=$$?; docker compose rm -sf db_test > /dev/null 2>&1; exit $$status

test-unit: ## Run unit tests only (no database required)
	./vendor/bin/phpunit tests/Unit/Models tests/Unit/Core

test-integration: db-test-up ## Run integration tests (starts test database automatically)
	@./vendor/bin/phpunit tests/Integration; status=$$?; docker compose rm -sf db_test > /dev/null 2>&1; exit $$status

serve: ## Start PHP development server
	php -S localhost:8000 -t public public/router.php

install: ## Install dependencies and setup environment
	composer install
	@if [ ! -f .env ]; then cp .env.example .env; fi
	@echo "Setup complete! Run 'make up' to start the database."
