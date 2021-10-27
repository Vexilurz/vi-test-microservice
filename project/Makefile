#SHELL := /bin/bash

.PHONY: tests_prepare
tests_prepare:
    APP_ENV=test symfony console doctrine:database:create
    APP_ENV=test symfony console doctrine:migrations:migrate -n
    APP_ENV=test symfony console doctrine:fixtures:load -n
