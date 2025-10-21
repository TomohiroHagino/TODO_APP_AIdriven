<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('タスク詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Title -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 {{ $todo->getStatus()->isDone() ? 'line-through text-gray-500' : '' }}">
                            {{ $todo->getTitle()->getValue() }}
                        </h3>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <span class="text-gray-600 dark:text-gray-400">ステータス:</span>
                        @if ($todo->getStatus()->isDone())
                            <span class="ml-2 px-3 py-1 text-sm font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                ✓ 完了
                            </span>
                        @else
                            <span class="ml-2 px-3 py-1 text-sm font-semibold rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                未完了
                            </span>
                        @endif
                    </div>

                    <!-- Created At -->
                    <div class="mb-6">
                        <span class="text-gray-600 dark:text-gray-400">作成日:</span>
                        <span class="ml-2 text-gray-900 dark:text-gray-100">
                            {{ $todo->getCreatedAt()->format('Y年m月d日 H:i') }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('todos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            ← 一覧に戻る
                        </a>

                        <form action="{{ route('todos.toggle', $todo->getId()->getValue()) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <x-secondary-button type="submit">
                                {{ $todo->getStatus()->isDone() ? '未完了にする' : '完了にする' }}
                            </x-secondary-button>
                        </form>

                        <a href="{{ route('todos.edit', $todo->getId()->getValue()) }}">
                            <x-primary-button>
                                編集
                            </x-primary-button>
                        </a>

                        <form action="{{ route('todos.destroy', $todo->getId()->getValue()) }}" method="POST" 
                              onsubmit="return confirm('本当に削除しますか？');" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit">
                                削除
                            </x-danger-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
