Run :
    composer dump-autoload
    composer update

Run php server with : 
    php -S localhost:8000

To kill process :
    fuser -k 8000/tcp

Run tests :
    ./vendor/bin/phpunit tests --display-deprecations