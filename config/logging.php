<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | デフォルトのログチャンネル
    |--------------------------------------------------------------------------
    |
    | このオプションでは、ログメッセージを書き込むために使用される
    | デフォルトのログチャンネルを定義します。
    | ここで指定した値は、下記「channels」設定リストに存在する
    | チャンネル名のいずれかと一致している必要があります。
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | 非推奨機能のログチャンネル
    |--------------------------------------------------------------------------
    |
    | このオプションでは、PHPやライブラリの非推奨機能に関する警告を
    | 記録するログチャンネルを制御します。
    | これにより、依存関係の将来のメジャーバージョンに備えて
    | アプリケーションの準備を行うことができます。
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | ログチャンネル設定
    |--------------------------------------------------------------------------
    |
    | ここでは、アプリケーションで使用するログチャンネルを設定します。
    | Laravel は PHP の Monolog ライブラリを利用しており、
    | 多彩で強力なハンドラーやフォーマッターを使用できます。
    |
    | 利用可能なドライバー:
    | "single", "daily", "slack", "syslog",
    | "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        /*
        |--------------------------------------------------------------------------
        | スタックチャンネル
        |--------------------------------------------------------------------------
        | 複数のチャンネルをまとめて使用できる「スタック」ドライバです。
        | ここで指定された複数チャンネルへ同時にログを送ります。
        |
        */
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', (string) env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | 単一ファイルログ
        |--------------------------------------------------------------------------
        | すべてのログを1つのファイル（laravel.log）に出力します。
        |
        */
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | 日次ローテーションログ
        |--------------------------------------------------------------------------
        | ログを毎日新しいファイルに出力し、古いファイルは一定期間後に削除します。
        |
        */
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14), // 保存期間（日数）
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Slack通知ログ
        |--------------------------------------------------------------------------
        | 指定したSlackチャンネルにエラーログを通知します。
        |
        */
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Papertrailログ
        |--------------------------------------------------------------------------
        | Papertrailなどの外部ログサービスにUDP経由で送信します。
        |
        */
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        /*
        |--------------------------------------------------------------------------
        | 標準エラー出力（stderr）
        |--------------------------------------------------------------------------
        | コンソールやサーバーログに標準エラー出力として書き込みます。
        |
        */
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        /*
        |--------------------------------------------------------------------------
        | システムログ（syslog）
        |--------------------------------------------------------------------------
        | OSのシステムログ機能を利用してログを記録します。
        |
        */
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | PHPのエラーログ
        |--------------------------------------------------------------------------
        | PHPの error_log() 関数を利用してログを出力します。
        |
        */
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Nullハンドラ
        |--------------------------------------------------------------------------
        | すべてのログを無視（破棄）する設定です。
        | テスト環境などで使用されます。
        |
        */
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        /*
        |--------------------------------------------------------------------------
        | 緊急ログ
        |--------------------------------------------------------------------------
        | すべてのログチャンネルが利用できない場合に使用される
        | フォールバック用の緊急ログです。
        |
        */
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];
