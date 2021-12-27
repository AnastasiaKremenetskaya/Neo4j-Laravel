#composer create-project laravel/laravel /app/neo4j --prefer-dist
cd /app/neo4j
#composer require "everyman/neo4jphp" "dev-master"
composer update
php /app/neo4j/artisan serve --host 0.0.0.0
