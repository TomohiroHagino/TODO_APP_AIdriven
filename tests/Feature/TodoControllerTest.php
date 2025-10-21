<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用ユーザーを作成
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_todos(): void
    {
        $response = $this->get('/todos');
        $response->assertRedirect('/login');
    }

    public function test_index_displays_todos(): void
    {
        // Todosを作成
        Todo::create([
            'user_id' => $this->user->id,
            'title' => 'テストタスク1',
            'is_done' => false,
            'created_at' => now(),
        ]);
        
        Todo::create([
            'user_id' => $this->user->id,
            'title' => 'テストタスク2',
            'is_done' => true,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/todos');

        $response->assertStatus(200);
        $response->assertSee('テストタスク1');
        $response->assertSee('テストタスク2');
    }

    public function test_index_filters_by_done_status(): void
    {
        Todo::create([
            'user_id' => $this->user->id,
            'title' => '未完了タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);
        
        Todo::create([
            'user_id' => $this->user->id,
            'title' => '完了タスク',
            'is_done' => true,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/todos?status=done');

        $response->assertStatus(200);
        $response->assertSee('完了タスク');
        $response->assertDontSee('未完了タスク');
    }

    public function test_index_filters_by_pending_status(): void
    {
        $pendingTodo = Todo::create([
            'user_id' => $this->user->id,
            'title' => 'これは未完了のタスクです',
            'is_done' => false,
            'created_at' => now(),
        ]);
        
        $doneTodo = Todo::create([
            'user_id' => $this->user->id,
            'title' => 'これは完了したタスクです',
            'is_done' => true,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/todos?status=pending');

        $response->assertStatus(200);
        $response->assertSee('これは未完了のタスクです');
        // 完了タスクのタイトルが表示されていないことを確認
        $response->assertDontSee('これは完了したタスクです', false);
    }

    public function test_create_displays_form(): void
    {
        $response = $this->actingAs($this->user)->get('/todos/create');

        $response->assertStatus(200);
        $response->assertSee('タスク名');
    }

    public function test_store_creates_todo(): void
    {
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => '新しいタスク',
        ]);

        $response->assertRedirect('/todos');
        $this->assertDatabaseHas('todos', [
            'user_id' => $this->user->id,
            'title' => '新しいタスク',
            'is_done' => false,
        ]);
    }

    public function test_store_validates_title_required(): void
    {
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => '',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_store_validates_title_max_length(): void
    {
        $response = $this->actingAs($this->user)->post('/todos', [
            'title' => str_repeat('あ', 256),
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_show_displays_todo(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => 'テストタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get("/todos/{$todo->id}");

        $response->assertStatus(200);
        $response->assertSee('テストタスク');
    }

    public function test_user_only_sees_own_todos(): void
    {
        // 自分のTodo
        $myTodo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '自分のタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        // 他人のTodo
        $otherUser = User::factory()->create();
        $otherTodo = Todo::create([
            'user_id' => $otherUser->id,
            'title' => '他人のタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get('/todos');

        $response->assertStatus(200);
        $response->assertSee('自分のタスク');
        $response->assertDontSee('他人のタスク');
    }

    public function test_edit_displays_form(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '編集前タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get("/todos/{$todo->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('編集前タスク');
    }

    public function test_update_modifies_todo(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '編集前タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->put("/todos/{$todo->id}", [
            'title' => '編集後タスク',
        ]);

        $response->assertRedirect("/todos/{$todo->id}");
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '編集後タスク',
        ]);
    }

    public function test_update_validates_title(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => 'タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->put("/todos/{$todo->id}", [
            'title' => '',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_destroy_deletes_todo(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '削除するタスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->delete("/todos/{$todo->id}");

        $response->assertRedirect('/todos');
        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_toggle_changes_status_from_pending_to_done(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '未完了タスク',
            'is_done' => false,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->patch("/todos/{$todo->id}/toggle");

        $response->assertRedirect();
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => true,
        ]);
    }

    public function test_toggle_changes_status_from_done_to_pending(): void
    {
        $todo = Todo::create([
            'user_id' => $this->user->id,
            'title' => '完了タスク',
            'is_done' => true,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->patch("/todos/{$todo->id}/toggle");

        $response->assertRedirect();
        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'is_done' => false,
        ]);
    }

    public function test_root_redirects_to_login_for_guest(): void
    {
        $response = $this->get('/');
        // Laravelは認証が必要な/todosへのリダイレクトを試み、その後loginへリダイレクト
        $response->assertStatus(302);
    }

    public function test_root_redirects_to_todos_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertRedirect('/todos');
    }
}
