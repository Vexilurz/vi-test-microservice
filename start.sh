#!/bin/bash


printf "\n\n   *** EXECUTING composer update *** \n\n"
composer update

printf "\n\n   *** APPLYING migrations to main database *** \n\n"
symfony console doctrine:migrations:migrate -n

printf "\n\n   *** EXECUTING test database re-create and migrations *** \n\n"
printf "... drop test database:"
APP_ENV=test symfony console doctrine:database:drop --force
printf "... create test database:"
APP_ENV=test symfony console doctrine:database:create
printf "... apply migrations to test database:"
APP_ENV=test symfony console doctrine:migrations:migrate -n
printf "... apply fixtures to test database:"
APP_ENV=test symfony console doctrine:fixtures:load -n

printf "\n\n   *** EXECUTING php-fpm *** \n\n"
php-fpm