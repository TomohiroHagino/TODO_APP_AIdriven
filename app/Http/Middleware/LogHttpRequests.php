<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTPリクエスト/レスポンスをRails風にログ出力するミドルウェア
 */
class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // .well-known リクエストは無視
        if (str_starts_with($request->path(), '.well-known')) {
            return $next($request);
        }

        $startTime = microtime(true);
        
        // リクエスト開始ログ
        $this->logRequestStart($request);
        
        // リクエスト処理
        $response = $next($request);
        
        // レスポンス完了ログ
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $this->logRequestComplete($response, $duration);
        
        return $response;
    }
    
    /**
     * リクエスト開始ログ（Rails風）
     */
    private function logRequestStart(Request $request): void
    {
        $method = $request->method();
        $uri = $request->getRequestUri();
        $ip = $request->ip();
        $timestamp = now()->format('Y-m-d H:i:s');
        
        // Started GET "/todos" for 127.0.0.1 at 2025-10-21 12:00:00
        $message = sprintf(
            "\n\033[1;36mStarted %s \"%s\" for %s at %s\033[0m",
            $method,
            $uri,
            $ip,
            $timestamp
        );
        
        $this->output($message);
        
        // コントローラーアクション
        $action = $request->route() ? $request->route()->getActionName() : 'Closure';
        $this->output(sprintf("  Processing by %s", $this->formatAction($action)));
        
        // パラメータ
        $params = $this->getParams($request);
        if (!empty($params)) {
            $this->output(sprintf("  Parameters: %s", json_encode($params, JSON_UNESCAPED_UNICODE)));
        }
    }
    
    /**
     * レスポンス完了ログ
     */
    private function logRequestComplete(Response $response, float $duration): void
    {
        $statusCode = $response->getStatusCode();
        $statusText = Response::$statusTexts[$statusCode] ?? 'Unknown';
        
        // ステータスコードに応じて色を変える
        $color = $this->getStatusColor($statusCode);
        
        // Completed 200 OK in 10ms
        $message = sprintf(
            "  \033[%smCompleted %d %s in %.2fms\033[0m\n",
            $color,
            $statusCode,
            $statusText,
            $duration
        );
        
        $this->output($message);
    }
    
    /**
     * パラメータを取得（機密情報は除外＋送信有無を記録）
     */
    private function getParams(Request $request): array
    {
        $params = [];
        $sensitiveKeys = $this->getSensitiveKeys();
        
        // クエリパラメータ
        if ($request->query()) {
            $queryParams = $request->query();
            // 機密情報を除外
            foreach ($sensitiveKeys as $key) {
                unset($queryParams[$key]);
            }
            $params = array_merge($params, $queryParams);
        }
        
        // POSTパラメータ（パスワードなど機密情報は除外）
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            // 機密情報を除外してパラメータ取得
            $input = $request->except($sensitiveKeys);
            $params = array_merge($params, $input);
            
            // 機密キーが送信されていた場合は、キーの存在だけ記録（値は取得しない）
            foreach ($sensitiveKeys as $key) {
                if ($request->has($key)) {
                    $params[$key] = '[FILTERED]';
                }
            }
        }
        
        // ルートパラメータ
        if ($request->route()) {
            $routeParams = $request->route()->parameters();
            if (!empty($routeParams)) {
                $params = array_merge($params, $routeParams);
            }
        }
        
        return $params;
    }
    
    /**
     * 機密情報のキーリストを取得
     */
    private function getSensitiveKeys(): array
    {
        return [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            '_token',
            'api_token',
            'secret',
            'api_key',
        ];
    }
    
    /**
     * アクション名をフォーマット
     */
    private function formatAction(string $action): string
    {
        // App\Http\Controllers\TodoController@index → TodoController#index
        $action = str_replace('App\Http\Controllers\\', '', $action);
        $action = str_replace('@', '#', $action);
        return $action;
    }
    
    /**
     * ステータスコードに応じた色を取得
     */
    private function getStatusColor(int $statusCode): string
    {
        if ($statusCode >= 200 && $statusCode < 300) {
            return '1;32'; // 緑（成功）
        } elseif ($statusCode >= 300 && $statusCode < 400) {
            return '1;33'; // 黄色（リダイレクト）
        } elseif ($statusCode >= 400 && $statusCode < 500) {
            return '1;31'; // 赤（クライアントエラー）
        } elseif ($statusCode >= 500) {
            return '1;35'; // マゼンタ（サーバーエラー）
        }
        return '0'; // デフォルト
    }
    
    /**
     * 標準出力に出力
     */
    private function output(string $message): void
    {
        // 開発環境でのみ標準出力に出力
        if (app()->environment('local')) {
            // php://stdout ストリームを使用（Webコンテキストでも動作）
            $stdout = fopen('php://stdout', 'w');
            if ($stdout) {
                fwrite($stdout, $message . PHP_EOL);
                fclose($stdout);
            }
        }
        
        // ログファイルにも記録
        Log::info($message);
    }
}
