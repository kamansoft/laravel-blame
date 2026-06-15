# Laravel Blame Dockerized Development Environment

This repository uses Laravel Sail inside each Laravel test project under `laravel-test-projects/`.

The root `docker/` folder contains shared Sail customization files and a small wrapper that proxies Sail commands to the Laravel 13 test project.

```text
docker/
  php/
    php.ini
    xdebug.ini
  sail/
    README.md
    laravel-13
```

## Laravel 13 test project

The first Sail test project is:

```text
laravel-test-projects/laravel-13/
```

Run commands from that project directory:

```bash
cd laravel-test-projects/laravel-13
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail test
./vendor/bin/sail artisan blame:set:systemuser --key=1
```

Or use the root wrapper:

```bash
./docker/sail/laravel-13 up -d
./docker/sail/laravel-13 artisan migrate
./docker/sail/laravel-13 test
./docker/sail/laravel-13 artisan blame:set:systemuser --key=1
```

## Standard Sail commands

The Laravel 13 test project supports the normal Sail command set:

```bash
sail up
sail up -d
sail down
sail down -v
sail ps
sail logs
sail shell
sail root-shell
sail build --no-cache
sail composer install
sail composer update
sail artisan migrate
sail artisan test
sail npm install
sail npm run dev
```

## Development ports

The Laravel 13 project uses host ports that avoid conflicts with other local Laravel apps:

```env
APP_PORT=8013
FORWARD_DB_PORT=3313
FORWARD_REDIS_PORT=6383
FORWARD_MAILPIT_PORT=8033
FORWARD_MAILPIT_DASHBOARD_PORT=8043
FORWARD_SELENIUM_PORT=4453
FORWARD_SELENIUM_VNC_PORT=7913
```
