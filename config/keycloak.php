<?php

return [
    'realm_public_key' => env('KEYCLOAK_REALM', null),

    'token_encryption_algorithm' => env('KEYCLOAK_TOKEN_ENCRYPTION_ALGORITHM', 'RS256'),

    'load_user_from_database' => env('KEYCLOAK_LOAD_USER_FROM_DATABASE', false),

    'user_provider_custom_retrieve_method' => env('KEYCLOAK_USER_PROVIDER_CUSTOM_RETRIEVE_METHOD', null),

    'user_provider_credential' => env('KEYCLOAK_USER_PROVIDER_CREDENTIAL', 'username'),

    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'preferred_username'),

    'append_decoded_token' => env('KEYCLOAK_APPEND_DECODED_TOKEN', false),

    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', 'account'),

    'ignore_resources_validation' => env('KEYCLOAK_IGNORE_RESOURCES_VALIDATION', false),

    'leeway' => env('KEYCLOAK_LEEWAY', 60),

    'input_key' => env('KEYCLOAK_TOKEN_INPUT_KEY', null),

    'base_url' => env('KEYCLOAK_BASE_URL', 'http://172.16.0.9:8080/auth/'),

    'client_id' => env('KEYCLOAK_CLIENT_ID', 'asm'),
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', 'fd0651d2-f924-4914-9ee4-d53c91125446'),

    'cache_openid' => env('KEYCLOAK_CACHE_OPENID', false),
];
