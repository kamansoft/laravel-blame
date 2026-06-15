# Laravel 13 Sail Test Project for laravel-blame

This project is a real Laravel 13 application used to test `kamansoft/laravel-blame` with Laravel Sail.

The package is installed as a Composer path dependency from the repository root, so changes to the package source are immediately reflected inside this test project.

## Start

From this directory:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
```

The app is available at `http://localhost:8013`.

## Run package tests

```bash
./vendor/bin/sail test
./vendor/bin/sail artisan test --filter LaravelBlamePackageTest
```

## Run package commands

```bash
./vendor/bin/sail artisan blame:set:systemuser --key=1
./vendor/bin/sail artisan blame:make:migration posts
```

## Useful Sail commands

```bash
./vendor/bin/sail ps
./vendor/bin/sail logs
./vendor/bin/sail shell
./vendor/bin/sail composer update
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```
