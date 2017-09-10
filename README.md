# optimal-route
Find optimal route for provided geo loactions

Objective : Find optimal route for given input coordinates using google directions api

Tech stack :
PHP
Nginx
MongoDb

Framework:
Laravel

Exposed endpoints:

1. localhost/api/route/${token} - GET
2. localhost/api/route - POST

Cron jobs:

1. Generate optimal route
2. Delete old routes from the history

Setup environment :

1.Run composer

  optimal-route/src/composer install --require-dev

2.Run dockers from the dockerfile under optimal-route folder

  docker-composer build 
  docker-composer up -d

  Docker exposed port required to be free:

  1. 80 - http
  2. 443 - https
  3. 27017 - mongodb
