**vi-test-microservice**
-

Launch steps:

- copy `.env.example` to `.env` and change contents if needed
- execute `docker-compose up -d --build`
- wait until "php" container finish to install composer dependencies and start to listening requests
- execute `symfony console doctrine:migrations:migrate`

To apply fixtures:

- execute `symfony console doctrine:fixtures:load`