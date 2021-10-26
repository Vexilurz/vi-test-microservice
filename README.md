**vi-test-microservice**
-

Launch steps:

- copy `.env.example` to `.env` and change contents if needed
- execute `docker-compose up -d --build`
- wait until "vitm-php" container finish to install composer dependencies and start to listening requests
- enter into "vitm-php" container: `docker exec -it vitm-php /bin/bash`
- execute `symfony console doctrine:migrations:migrate`

  
To apply fixtures:

- enter into "vitm-php" container: `docker exec -it vitm-php /bin/bash`
- execute `symfony console doctrine:fixtures:load`


  **!!! Before run tests: !!!**

- enter into "vitm-php" container: `docker exec -it vitm-php /bin/bash`
- execute `APP_ENV=test symfony console doctrine:database:create`
- execute `APP_ENV=test symfony console doctrine:migrations:migrate -n`
- execute `APP_ENV=test symfony console doctrine:fixtures:load -n`