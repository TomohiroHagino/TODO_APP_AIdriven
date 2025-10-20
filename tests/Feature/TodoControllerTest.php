<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\TodoModel;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * テスト: 一覧ページが表示される
     */
    public function test_index_displays_todos(): void
    {
        // タスクを作成
        TodoModel::create([
            'title' => 'テストタスク1',
            'is_done' => false,
            'created_at' => now(),
        ]);

        TodoModel::create([
            'title' => 'テストタスク2',
            'is_done' => true,
            'created_at' => now(),
        ]);

        // 一覧ページにアクセス
        $response = $this->get('/todos');

        // ステータス200が返る
        $response->assertStatus(200);

        // タスクが表示される
        $response->assertSee('テストタスク1');
        $response->assertSee('テストタスク2');
    }

    /**
     * テスト: 完了フィルターが機能する
     */
    public function test_index_filters_by_done_status(): void
    {
        TodoModel::create([
            'title' => '未完了タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        TodoModel::create([
            'title' => '完了タスク',
            'is_done' => true,
            'created_at' => now(),
        ]);

        // 完了タスクのみ表示
        $response = $this->get('/todos?status=done');

        $response->assertStatus(200);
        $response->assertSee('完了タスク');
        $response->assertDontSee('未完了タスク');
    }

    /**
     * テスト: 未完了フィルターが機能する
     */
    public function test_index_filters_by_pending_status(): void
    {
        TodoModel::create([
            'title' => '買い物に行く',
            'is_done' => false,
            'created_at' => now(),
        ]);

        TodoModel::create([
            'title' => '掃除を終わらせる',
            'is_done' => true,
            'created_at' => now(),
        ]);

        // 未完了タスクのみ表示
        $response = $this->get('/todos?status=pending');

        $response->assertStatus(200);
        $response->assertSee('買い物に行く');
        $response->assertDontSee('掃除を終わらせる');
    }

    /**
     * テスト: 新規作成ページが表示される
     */
    public function test_create_displays_form(): void
    {
        $response = $this->get('/todos/create');

        $response->assertStatus(200);
        $response->assertSee('新しいタスクを作成');
    }

    /**
     * テスト: タスクを作成できる
     */
    public function test_store_creates_todo(): void
    {
        $response = $this->post('/todos', [
            'title' => '新しいタスク',
        ]);

        // リダイレクトされる
        $response->assertRedirect('/todos');
        $response->assertSessionHas('success', 'タスクを作成しました');

        // データベースに保存される
        $this->assertDatabaseHas('todos', [
            'title' => '新しいタスク',
            'is_done' => false,
        ]);
    }

    /**
     * テスト: タイトルが空の場合はバリデーションエラー
     */
    public function test_store_validates_title_required(): void
    {
        $response = $this->post('/todos', [
            'title' => '',
        ]);

        $response->assertSessionHasErrors('title');
        
        // データベースに保存されない
        $this->assertDatabaseCount('todos', 0);
    }

    /**
     * テスト: タイトルが255文字を超える場合はバリデーションエラー
     */
    public function test_store_validates_title_max_length(): void
    {
        $response = $this->post('/todos', [
            'title' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors('title');
    }

    /**
     * テスト: 詳細ページが表示される
     */
    public function test_show_displays_todo(): void
    {
        $todo = TodoModel::create([
            'title' => '詳細表示テスト',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->get("/todos/{$todo->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細表示テスト');
        $response->assertSee('未完了');
    }

    /**
     * テスト: 存在しないタスクの詳細ページは404
     */
    public function test_show_returns_404_for_nonexistent_todo(): void
    {
        $response = $this->get('/todos/999');

        $response->assertStatus(404);
    }

    /**
     * テスト: 編集ページが表示される
     */
    public function test_edit_displays_form(): void
    {
        $todo = TodoModel::create([
            'title' => '編集前のタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->get("/todos/{$todo->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('編集前のタスク');
        $response->assertSee('タスクを編集');
    }

    /**
     * テスト: タスクを更新できる
     */
    public function test_update_modifies_todo(): void
    {
        $todo = TodoModel::create([
            'title' => '更新前のタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->put("/todos/{$todo->id}", [
            'title' => '更新後のタスク',
        ]);

        // リダイレクトされる
        $response->assertRedirect("/todos/{$todo->id}");
        $response->assertSessionHas('success', 'タスクを更新しました');

        // データベースが更新される
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '更新後のタスク',
        ]);
    }

    /**
     * テスト: 更新時もバリデーションが機能する
     */
    public function test_update_validates_title(): void
    {
        $todo = TodoModel::create([
            'title' => '元のタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->put("/todos/{$todo->id}", [
            'title' => '',
        ]);

        $response->assertSessionHasErrors('title');

        // データベースは変更されない
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '元のタスク',
        ]);
    }

    /**
     * テスト: タスクを削除できる
     */
    public function test_destroy_deletes_todo(): void
    {
        $todo = TodoModel::create([
            'title' => '削除するタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->delete("/todos/{$todo->id}");

        // リダイレクトされる
        $response->assertRedirect('/todos');
        $response->assertSessionHas('success', 'タスクを削除しました');

        // データベースから削除される
        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * テスト: 完了状態を切り替えられる（未完了→完了）
     */
    public function test_toggle_changes_status_from_pending_to_done(): void
    {
        $todo = TodoModel::create([
            'title' => '切替テスト',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->post("/todos/{$todo->id}/toggle");

        // リダイレクトされる
        $response->assertRedirect('/todos');
        $response->assertSessionHas('success', 'タスクの状態を変更しました');

        // 完了状態になる
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => true,
        ]);
    }

    /**
     * テスト: 完了状態を切り替えられる（完了→未完了）
     */
    public function test_toggle_changes_status_from_done_to_pending(): void
    {
        $todo = TodoModel::create([
            'title' => '切替テスト',
            'is_done' => true,
            'created_at' => now(),
        ]);

        $response = $this->post("/todos/{$todo->id}/toggle");

        // 未完了状態になる
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => false,
        ]);
    }

    /**
     * テスト: 存在しないタスクの切替は404
     */
    public function test_toggle_returns_404_for_nonexistent_todo(): void
    {
        $response = $this->post('/todos/999/toggle');

        $response->assertStatus(404);
    }

    /**
     * テスト: ルートアクセスはTodo一覧にリダイレクト
     */
    public function test_root_redirects_to_todos_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/todos');
    }
}

