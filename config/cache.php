<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | デフォルトのキャッシュストア
    |--------------------------------------------------------------------------
    |
    | このオプションは、フレームワークが使用するデフォルトの
    | キャッシュストアを制御します。
    | アプリケーション内でキャッシュ操作を実行する際に、
    | 別のストアが明示的に指定されていない場合、
    | ここで設定した接続が使用されます。
    |
    */

    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | キャッシュストアの設定
    |--------------------------------------------------------------------------
    |
    | ここではアプリケーションで利用するすべてのキャッシュ「ストア」と
    | それぞれのドライバを定義できます。
    | 同じキャッシュドライバに対して複数のストアを定義し、
    | キャッシュするアイテムの種類ごとにグループ化することも可能です。
    |
    | 利用可能なドライバ:
    | "array", "database", "file", "memcached",
    | "redis", "dynamodb", "octane", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'),
            'table' => env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | キャッシュキーのプレフィックス
    |--------------------------------------------------------------------------
    |
    | APC、Database、Memcached、Redis、DynamoDBなどの
    | キャッシュストアを利用する場合、同じキャッシュサーバを
    | 他のアプリケーションが使用している可能性があります。
    | そのため、キャッシュキーの衝突を避けるために、
    | すべてのキーにプレフィックスを付けることができます。
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug((string) env('APP_NAME', 'laravel')).'-cache-'),

];
