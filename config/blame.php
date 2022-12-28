<?php

// config for Kamansoft/LaravelBlame
return [
    'system_user_id' => env('BLAME_SYSTEM_USER_ID'), //do not modify this line
    'system_user_name' => env('BLAME_SYSTEM_USER_NAME', 'system'),
    'system_user_email' => env('BLAME_SYSTEM_USER_EMAIL', 'system'.'@'.explode('/', config('app.url'))[2]),
    'create_by_field_name' => 'created_by',
    'update_by_field_name' => 'updated_by',

];
