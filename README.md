# la-server
Laravel-based server

## Table of Contents

* [Project Creation](#project-creation)
* [Setup](#setup)
* [Maintenance Mode](#maintenance-mode)
* [Unit Testing](#unit-testing)
* [CLI](#cli)
* [Setting Environment](#setting-environment)
* [Deployment](#deployment)
* [CSRF Protection](#csrf-protection)

## Project Creation
* Use Composer to install Laravel
1. `composer create-project laravel/laravel la-server`
2. If a repo has already been made and cloned, it might be needed to copy the content of __/la-server__ into this cloned folder
3. If using Sanctum for authentication https://laravel.com/docs/8.x/sanctum#installation
    > `composer require laravel/sanctum`
    > `php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`
4. Serve using `php artisan serve`

## Setup
1. `composer install`
2. Change timezone in __/config/app.php__
3. Change `APP_KEY` in __.env__ to a 32 character string
4. Configure database credentials in __.env__
    + If the DB password has symbols like '#', wrap the password in speech marks ex: `DB_PASSWORD="dfg555###"` otherwise '#' breaks the text
5. Database setup and seeding
    > `php artisan migrate`
    > `php artisan db:seed`

## Maintenance Mode
* App can be switched into maintenance mode by runnning
    > `php artisan down`
* To bring app back into live mode, run
    > `php artisan up`
* Various options can be configured that change the behaviour during maintenance mode
    + https://laravel.com/docs/8.x/configuration#maintenance-mode

## Unit Testing
* To run tests stored in the __/tests__ directory, use
    > `php artisan test`
* https://laravel.com/docs/8.x/structure#the-tests-directory

## CLI
* Console routes can be defined in __/routes/console.php__
* Console commands can be generated using
    > `php artisan make:command "<command-name"`
* A routed command can be executed using
    > `php artisan <command-name>`
    > `php artisan ispire`
* A list of all available routes can be printed using
    > `php artisan route:list`

### Setting Environment
* If global PHP version is different, locally PHP environment can be set
> `export PATH=/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin:/Library/Apple/usr/bin:/Applications/MAMP/bin/php/php7.4.16/bin`
> `composer create-project --prefer-dist laravel/laravel la-server2 "8.*"`
    + https://stackoverflow.com/a/63111813

## Deployment
* Laravel app can be deployed to shared hosting
1. Clone the repo
2. Run `composer install`
3. Execute other setup operations
* App can be accessed at _http://example.com/laravel-folder/public_
    + i.e. the application can be accessed at the __/public__ sub directory (sub route)

## CSRF Protection
* CSRF protection can be completely disabled for *all routes* (https://laravel.com/docs/8.x/csrf#csrf-excluding-uris)
    + Add '*' to the `$except` array of __Http/Middleware/VerifyCsrfToken.php__
    + Beware of the security implications!
