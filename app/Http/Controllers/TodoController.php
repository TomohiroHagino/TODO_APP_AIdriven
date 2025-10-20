<?php

namespace App\Http\Controllers;

use App\Application\Todo\Service\CreateTodoService;
use App\Application\Todo\Service\DeleteTodoService;
use App\Application\Todo\Service\GetTodoDetailService;
use App\Application\Todo\Service\ListTodosService;
use App\Application\Todo\Service\ToggleTodoStatusService;
use App\Application\Todo\Service\UpdateTodoService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Todoコントローラー
 * ユースケース（アプリケーションサービス）を呼び出してビューやリダイレクトを返す
 */
class TodoController extends Controller
{
    private ListTodosService $listTodosService;
    private CreateTodoService $createTodoService;
    private GetTodoDetailService $getTodoDetailService;
    private UpdateTodoService $updateTodoService;
    private ToggleTodoStatusService $toggleTodoStatusService;
    private DeleteTodoService $deleteTodoService;

    public function __construct(
        ListTodosService $listTodosService,
        CreateTodoService $createTodoService,
        GetTodoDetailService $getTodoDetailService,
        UpdateTodoService $updateTodoService,
        ToggleTodoStatusService $toggleTodoStatusService,
        DeleteTodoService $deleteTodoService
    ) {
        $this->listTodosService = $listTodosService;
        $this->createTodoService = $createTodoService;
        $this->getTodoDetailService = $getTodoDetailService;
        $this->updateTodoService = $updateTodoService;
        $this->toggleTodoStatusService = $toggleTodoStatusService;
        $this->deleteTodoService = $deleteTodoService;
    }

    /**
     * Todo一覧を表示
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $status = $request->query('status'); // 'done', 'pending', または null
        
        if ($status === 'done') {
            $todos = $this->listTodosService->handleByStatus(true);
        } elseif ($status === 'pending') {
            $todos = $this->listTodosService->handleByStatus(false);
        } else {
            $todos = $this->listTodosService->handle();
        }

        return view('todos.index', [
            'todos' => $todos,
            'currentStatus' => $status,
        ]);
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
        ]);

        try {
            $this->createTodoService->handle($validated['title']);
            return redirect()->route('todos.index')
                ->with('success', 'タスクを作成しました');
        } catch (\InvalidArgumentException $e) {
            return back()
                ->withInput()
                ->withErrors(['title' => $e->getMessage()]);
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
        $todo = $this->getTodoDetailService->handle($id);

        if (!$todo) {
            abort(404, 'タスクが見つかりませんでした');
        }

        return view('todos.show', [
            'todo' => $todo,
        ]);
    }

    /**
     * 編集フォームを表示
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $todo = $this->getTodoDetailService->handle($id);

        if (!$todo) {
            abort(404, 'タスクが見つかりませんでした');
        }

        return view('todos.edit', [
            'todo' => $todo,
        ]);
    }

    /**
     * Todoを更新
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ], [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
        ]);

        try {
            $this->updateTodoService->handle($id, $validated['title']);
            return redirect()->route('todos.show', $id)
                ->with('success', 'タスクを更新しました');
        } catch (\RuntimeException $e) {
            abort(404, $e->getMessage());
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
        $this->deleteTodoService->handle($id);
        
        return redirect()->route('todos.index')
            ->with('success', 'タスクを削除しました');
    }

    /**
     * Todoの完了/未完了状態を切り替え
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function toggle(int $id): RedirectResponse
    {
        try {
            $this->toggleTodoStatusService->handle($id);
            return redirect()->route('todos.index')
                ->with('success', 'タスクの状態を変更しました');
        } catch (\RuntimeException $e) {
            abort(404, $e->getMessage());
        }
    }
}

