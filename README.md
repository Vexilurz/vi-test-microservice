**vi-test-microservice**
-

Launch steps:

- copy `.env.example` to `.env` and change contents if needed
- execute `docker-compose up -d --build`
- wait until "php" container finish to install composer dependencies
- execute `symfony console doctrine:migrations:migrate`
