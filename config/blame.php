<?php

return [
    'auth_guard' => env('BLAME_AUTH_GUARD'),
    'user_resolver' => null,
    'user_resolver_class' => null,
    'user_id_attribute' => env('BLAME_USER_ID_ATTRIBUTE', 'id'),
    'system_user_id' => env('BLAME_SYSTEM_USER_ID'),
    'system_user_resolver' => null,
    'system_user_resolver_class' => null,
    'system_user_name' => env('BLAME_SYSTEM_USER_NAME', 'system'),
    'system_user_email' => env('BLAME_SYSTEM_USER_EMAIL', 'system'.'@'.explode('/', config('app.url'))[2]),
    'created_by_field_name' => env('BLAME_CREATED_BY_FIELD', 'created_by'),
    'updated_by_field_name' => env('BLAME_UPDATED_BY_FIELD', 'updated_by'),
    'user_id_column_type' => env('BLAME_USER_ID_COLUMN_TYPE', 'unsignedBigInteger'),
    'env_file_path' => base_path('.env'),
];
