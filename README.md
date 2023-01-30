# laravel-blame

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kamansoft/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/kamansoft/laravel-blame)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/kamansoft/laravel-blame/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/kamansoft/laravel-blame/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/kamansoft/laravel-blame/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/kamansoft/laravel-blame/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/kamansoft/laravel-blame.svg?style=flat-square)](https://packagist.org/packages/kamansoft/laravel-blame)

## update_by and created_by fields in your laravel model ?

This is a laravel package that will ease the usage of the normally called **created_by** and **update_by** extra fields used to stablish responsabilites on records persistance create or update events,  with similar fashion as the "timestamp fields" on Eloquent's models, it automatically fills the fields with the primary key of the currently logged user or with a preconfigured system user id. 


## Requirements

This packages was build taking in mind that you use laravel with an [eloquent **user model class** as auth provider](https://laravel.com/docs/9.x/authentication#introduction). So before installing you must make sure that your user eloquent model is set on the providers section of your auth config file like this:

```php
//config/auth.php

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class, // <-- this is the important part
    ],
],
```


## Installation



You can install the package via composer:

```bash
composer require kamansoft/laravel-blame
```

Some times your laravel application will perform actions that persists records on the database without an authenticated or logged in user,  You can call this user as System User or any other name you want, but you must specify its id or primary key.

To do so you can run the [system user command](#system-user-command) or the  package install comand:

```bash
php artisan blame:install
```
The above command will publish the package config file and it will run the [system user command](#system-user-command) with no arguments. 


## Usage


You must first add the  **update_by** and **created_by** fields to the table's migration field in order to use it in your models.  

```php
//dabase/migrations/2023_01_02_154102_create_somes_table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('somes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');

            $system_user_id = env('BLAME_SYSTEM_USER_ID');
            $table->unsignedBigInteger('created_by')->default($system_user_id);
            $table->unsignedBigInteger('updated_by')->default($system_user_id);
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamps();
        });
    }
```

Then just make use of the [Modelblamer trait](https://github.com/kamansoft/laravel-blame/blob/c95967a0e15155562d1aa05a5fc6fb8e8d164ff8/src/Traits/ModelBlamer.php) on your eloquent models, and its [booting method](https://github.com/kamansoft/laravel-blame/blob/c95967a0e15155562d1aa05a5fc6fb8e8d164ff8/src/Traits/ModelBlamer.php#L11)  will take care of the rest. Also for the seek of a good practice let your model implement the [BlameableInterface](https://github.com/kamansoft/laravel-blame/blob/main/src/Contracts/ModelBlame.php) on your models. Then your model should look something like this 

```php
//app/Models/SomeModel.php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Kamansoft\LaravelBlame\Contracts\ModelBlame;
use Kamansoft\LaravelBlame\Traits\ModelBlamer;

class SomeModel extends Model implements ModelBlame
{

    use ModelBlamer; // <-- this is the important part
```




## Commands 

This package ships two artisan commands as tools to ease the package usage simplifiying repetitive task like creating or updating the systemuser or adding the updated by and created by fields to your model's table. 

### Blame FIelds Migration command

This command is primarly intend to be used when you need to update an existing table, adding the created_by and updated_by fields, it needs an argument with the name of the table to update.

```bash
php artisan blame:make:migration some_table_name
```
When runned the above command will create a new migration file in the database/migrations folder with the name **add_blame_fields_to_some_table_name_table** with a content similar to this:

```php
//database/migrations/2023_01_02_154102_add_blame_fields_to_some_table_name_table.php
return new class extends Migration
{
    public function up()
    {

        Schema::table('some_table_name', function (Blueprint $table) {
            $system_user_id = env('BLAME_SYSTEM_USER_ID');
            $table->unsignedBigInteger(config('blame.created_by_field_name'))->default($system_user_id);
            $table->unsignedBigInteger(config('blame.updated_by_field_name'))->default($system_user_id);
        });
        Schema::table('some_table_name', function (Blueprint $table) {
            $table->unsignedBigInteger(config('blame.created_by_field_name'))->default(null)->change();
            $table->unsignedBigInteger(config('blame.updated_by_field_name'))->default(null)->change();
        });
        Schema::table('some_table_name', function (Blueprint $table) {
            $table->foreign(config('blame.created_by_field_name'))->references('id')->on('users');
            $table->foreign(config('blame.updated_by_field_name'))->references('id')->on('users');
        });
    }
```


### System User Command

When used with no param or arguments:
```bash
php artisan blame:set:systemuser 
```
This command creates a new laravel user to be used when no user can be retrived with Auth::user(). 

When runned with a value throug the optional "--key" param:
```bash
php artisan blame:set:systemuser --key=some_user_id
```
The command will check if a user with that id exists and if not it will try to create one with that id.

In both cases the command will set the created system user primary key or id in the project .env file as BLAME_SYSTEM_USER_ID.

