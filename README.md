# sportofit
### Developed using Laravel 5.1 LTS
<img src="public/assets/logos/L5.png" alt="Image of Laravel" height="150" width="300"/>

### Installation guidelines

 - To clone repository use **git clone git@github.com:woxiprogrammers/sportofit.git**
 - There are two main branches
 ```
    1. master

    2. develop
 ```
 - always pull code from develop for local development.
 - create .env file in root directory and copy all content from .env.example to .env file and
   make changes according to requirement like database and other required passwords according
   to your local environment.
 - Install composer [It's dependency manager for php]
 ```
    1. curl -sS https://getcomposer.org/installer | php

    2. sudo mv composer.phar /usr/local/bin/composer
 ```
 - Then goto root path of project directory and run following command
 ```

    * composer install

 ```
 - Give recursive permission [777 -R] to following folders
 ```
    * bootstrap/cache

    * storage
 ```
 - Run following command to generate DB
 ```

    * php artisan key:generate

    * php artisan migrate --seed
 ```
  - Create a virtual host and point it to public directory of Project
  - Always create pull request on develop branch, don't do anything on master branch directly