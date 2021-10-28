**vi-test-microservice**
-

Launch steps:

- copy `.env.example` to `.env` and change contents if needed
- execute `docker-compose up -d --build`
- wait until "vitm-php" container finish to install composer dependencies, database migrations, and start to listening
  requests

To apply fixtures:

- enter into "vitm-php" container: `docker exec -it vitm-php /bin/bash`
- execute `symfony console doctrine:fixtures:load`

Open `start.sh` file to see what instrustions executed after "vitm-php" container start
