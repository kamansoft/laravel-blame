<?php

// config for Kamansoft/LaravelBlame
return [
    'system_user_name' => env('BLAME_SYSTEM_USER_NAME', 'system'),
    'system_user_email' => env('BLAME_SYSTEM_USER_EMAIL', 'system'.'@'.explode('/', config('app.url'))[2]),
    'created_by_field_name' => 'created_by',
    'updated_by_field_name' => 'updated_by',
];
