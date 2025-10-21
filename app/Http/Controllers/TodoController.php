<?php

namespace App\Http\Controllers;

use App\Application\UserAggregate\Service\AddTodoToUserService;
use App\Application\UserAggregate\Service\DeleteTodoOfUserService;
use App\Application\UserAggregate\Service\GetUserTodosService;
use App\Application\UserAggregate\Service\ToggleTodoStatusService;
use App\Application\UserAggregate\Service\UpdateTodoOfUserService;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Todoコントローラー（User Aggregate対応）
 * 
 * 認証ユーザーのTodoを管理
 * ユースケース（Application Service）を呼び出してビューやリダイレクトを返す
 */
class TodoController extends Controller
{
    private GetUserTodosService $getUserTodosService;
    private AddTodoToUserService $addTodoToUserService;
    private UpdateTodoOfUserService $updateTodoOfUserService;
    private ToggleTodoStatusService $toggleTodoStatusService;
    private DeleteTodoOfUserService $deleteTodoOfUserService;

    public function __construct(
        GetUserTodosService $getUserTodosService,
        AddTodoToUserService $addTodoToUserService,
        UpdateTodoOfUserService $updateTodoOfUserService,
        ToggleTodoStatusService $toggleTodoStatusService,
        DeleteTodoOfUserService $deleteTodoOfUserService
    ) {
        $this->getUserTodosService = $getUserTodosService;
        $this->addTodoToUserService = $addTodoToUserService;
        $this->updateTodoOfUserService = $updateTodoOfUserService;
        $this->toggleTodoStatusService = $toggleTodoStatusService;
        $this->deleteTodoOfUserService = $deleteTodoOfUserService;
    }

    /**
     * Todo一覧を表示
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $userId = auth()->id();
        $status = $request->query('status');

        try {
            if ($status === 'done') {
                $todos = $this->getUserTodosService->handleByStatus($userId, true);
            } elseif ($status === 'pending') {
                $todos = $this->getUserTodosService->handleByStatus($userId, false);
            } else {
                $todos = $this->getUserTodosService->handle($userId);
            }

            return view('todos.index', [
                'todos' => $todos,
                'currentStatus' => $status,
            ]);
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * 新規作成フォームを表示
     *
     * @return View
     */
    public function create(): View
    {
        return view('todos.create');
    }

    /**
     * 新しいTodoを保存
     *
     * @param StoreTodoRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $userId = auth()->id();

        try {
            $this->addTodoToUserService->handle($userId, $request->getTitle());
            return redirect()->route('todos.index')
                ->with('success', 'タスクを作成しました');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['title' => $e->getMessage()]);
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Todo詳細を表示
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $userId = auth()->id();

        try {
            $todos = $this->getUserTodosService->handle($userId);
            
            // 該当するTodoを検索
            $todo = null;
            foreach ($todos as $t) {
                if ($t->getId()->getValue() === $id) {
                    $todo = $t;
                    break;
                }
            }

            if (!$todo) {
                abort(404, 'Todoが見つかりません');
            }

            return view('todos.show', ['todo' => $todo]);
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * 編集フォームを表示
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $userId = auth()->id();

        try {
            $todos = $this->getUserTodosService->handle($userId);
            
            // 該当するTodoを検索
            $todo = null;
            foreach ($todos as $t) {
                if ($t->getId()->getValue() === $id) {
                    $todo = $t;
                    break;
                }
            }

            if (!$todo) {
                abort(404, 'Todoが見つかりません');
            }

            return view('todos.edit', ['todo' => $todo]);
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Todoを更新
     *
     * @param UpdateTodoRequest $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(UpdateTodoRequest $request, int $id): RedirectResponse
    {
        $userId = auth()->id();

        try {
            $this->updateTodoOfUserService->handle($userId, $id, $request->getTitle());
            return redirect()->route('todos.show', $id)
                ->with('success', 'タスクを更新しました');
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), '見つかりません')) {
                abort(404, $e->getMessage());
            }
            abort(500, $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['title' => $e->getMessage()]);
        }
    }

    /**
     * Todoを削除
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $userId = auth()->id();

        try {
            $this->deleteTodoOfUserService->handle($userId, $id);
            return redirect()->route('todos.index')
                ->with('success', 'タスクを削除しました');
        } catch (\RuntimeException $e) {
            abort(500, $e->getMessage());
        }
    }

    /**
     * Todoのステータスを切り替え
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function toggle(int $id): RedirectResponse
    {
        $userId = auth()->id();

        try {
            $this->toggleTodoStatusService->handle($userId, $id);
            return back()->with('success', 'ステータスを更新しました');
        } catch (\RuntimeException $e) {
            if (str_contains($e->getMessage(), '見つかりません')) {
                abort(404, $e->getMessage());
            }
            abort(500, $e->getMessage());
        }
    }
}
