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
* [Migration](#migration)
* [Role Management](#role-management)
* [AdminLTE](#adminlte)

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
    + Use `php artisan key:generate` to generate and populate the key field. Without this, 500 error will be shown
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
    + Sending requests without CSRF token when token-checking is enabled generates 419 errors

## Migration
* To view the status of existing migrations;
    > `php artisan migrate:status`
* Rollback the last migration
    > `php artisan migrate:rollback`
    > `php artisan migrate:rollback --step=1` - rolls back only the last migration
    > `php artisan migrate:rollback --step=1 --pretend` the `--pretend` option shows SQL that would run without actually executing the command
* Carry out all migrations that have not been run before
    > `php artisan migrate`

## Role Management
* This repo uses Spatie's _laravel-permission_ package for role management
* To install,
    1. Follow https://spatie.be/docs/laravel-permission/v5/prerequisites
    2. https://spatie.be/docs/laravel-permission/v5/installation-laravel
        + The provider had to be manually added
* Permissions are cached so they main persist even if the entire database is cleared
    + Run `php artisan optimize:clear` to clear cache
* To list all permissions/ roles
    + `php artisan permission:show`

### Using with Sanctum
* Since this template uses Sanctum, the default guard is sanctum (do `php artisan permission:show` and it will show the guard)
* When roles/ permissions are created (within a HTTP Controller), they are created for the 'sanctum' guard
    + However, during role checking, guard used seems to be the default provided in __/config/auth.php__
    + https://spatie.be/docs/laravel-permission/v3/basic-usage/multiple-guards#the-downside-to-multiple-guards
    + Due to this, make sure that models requiring permissions have:
    > `protected $guard_name = 'sanctum';`
* Despite this, when permissions are created within a seeder, the default guard is `web`
    + The User model is set to the `guard_name` 'web' 

### Seeding permissions and user access
* This feature can be used to initially commit roles and permissions
    > `php artisan db:seed RolesAndPermissionsSeeder`
* The UserAccessSeeder can be used to assign roles to some of the default accounts
    > `php artisan db:seed UserAccessSeeder`

## AdminLTE
* To publish resources 
    > `php artisan adminlte:install`
    + If resources already exist, a prompt is shown whether to replace existing configuration/ resources
* To add login/register forms
    > `composer require laravel/ui`
    > `php artisan ui bootstrap --auth`
    > `php artisan adminlte:install --only=auth_views`
