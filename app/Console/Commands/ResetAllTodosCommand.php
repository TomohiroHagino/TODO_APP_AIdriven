<?php

namespace App\Console\Commands;

use App\Application\UserAggregate\Service\ResetAllTodosService;
use Illuminate\Console\Command;

/**
 * 全Todoを未完了に戻すバッチコマンド
 * 
 * 使い方:
 *   php artisan todos:reset-all
 */
class ResetAllTodosCommand extends Command
{
    /**
     * コマンド名と引数の定義
     *
     * @var string
     */
    protected $signature = 'todos:reset-all
                            {--force : 確認なしで実行}';

    /**
     * コマンドの説明
     *
     * @var string
     */
    protected $description = '全ユーザーの全Todoのステータスを未完了に戻します';

    /**
     * Application Service
     */
    private ResetAllTodosService $service;

    /**
     * コンストラクタ
     */
    public function __construct(ResetAllTodosService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * コマンド実行
     */
    public function handle(): int
    {
        // 確認プロンプト（--force オプションがない場合）
        if (!$this->option('force')) {
            if (!$this->confirm('全てのTodoを未完了に戻します。よろしいですか？')) {
                $this->info('キャンセルしました。');
                return Command::SUCCESS;
            }
        }

        $this->info('処理を開始します...');
        $this->newLine();

        // 進捗バーを表示
        $progressBar = $this->output->createProgressBar();
        $progressBar->start();

        try {
            // Application Serviceを実行
            $result = $this->service->handle();

            $progressBar->finish();
            $this->newLine(2);

            // 結果を表示
            $this->info('✓ 処理が完了しました！');
            $this->newLine();
            
            $this->table(
                ['項目', '件数'],
                [
                    ['対象ユーザー数', $result['totalUsers']],
                    ['総Todo数', $result['totalTodos']],
                    ['リセットしたTodo数', $result['resetCount']],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $progressBar->finish();
            $this->newLine(2);
            
            $this->error('✗ エラーが発生しました: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            
            return Command::FAILURE;
        }
    }
}

