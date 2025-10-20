<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 既定のファイルシステムディスク
    |--------------------------------------------------------------------------
    |
    | フレームワークで使用する既定のファイルシステム・ディスクを
    | 指定します。「local」ディスクのほか、各種クラウドベースの
    | ディスクをストレージに利用できます。
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | ファイルシステム・ディスク
    |--------------------------------------------------------------------------
    |
    | 必要なだけ多くのファイルシステム・ディスクをここで設定できます。
    | 同じドライバに対して複数のディスクを設定することも可能です。
    | よく使われるドライバの例を参照として記載しています。
    |
    | 対応ドライバ: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | シンボリックリンク
    |--------------------------------------------------------------------------
    |
    | Artisan コマンド `storage:link` 実行時に作成するシンボリックリンクを
    | ここで設定します。配列のキーがリンクの作成先パス、値がリンク元
    | （ターゲット）のパスになります。
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
