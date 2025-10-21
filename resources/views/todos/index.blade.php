<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Todos') }}
            </h2>
            <a href="{{ route('todos.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                新規作成
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter Tabs -->
            <div class="mb-6">
                <div class="flex space-x-4 border-b border-gray-200 dark:border-gray-700">
                    <a href="{{ route('todos.index') }}" 
                       class="px-4 py-2 -mb-px {{ is_null($currentStatus) ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}">
                        すべて
                    </a>
                    <a href="{{ route('todos.index', ['status' => 'pending']) }}" 
                       class="px-4 py-2 -mb-px {{ $currentStatus === 'pending' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}">
                        未完了
                    </a>
                    <a href="{{ route('todos.index', ['status' => 'done']) }}" 
                       class="px-4 py-2 -mb-px {{ $currentStatus === 'done' ? 'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200' }}">
                        完了
                    </a>
                </div>
            </div>

            <!-- Todos List -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if (count($todos) === 0)
                    <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                        タスクがありません
                    </div>
                @else
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($todos as $todo)
                            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center flex-1">
                                        <!-- Toggle Button -->
                                        <form action="{{ route('todos.toggle', $todo->getId()->getValue()) }}" method="POST" class="mr-3">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="focus:outline-none">
                                                @if ($todo->getStatus()->isDone())
                                                    <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        <!-- Todo Info -->
                                        <div class="flex-1">
                                            <a href="{{ route('todos.show', $todo->getId()->getValue()) }}" 
                                               class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 {{ $todo->getStatus()->isDone() ? 'line-through text-gray-500 dark:text-gray-500' : '' }}">
                                                {{ $todo->getTitle()->getValue() }}
                                            </a>
                                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                作成日: {{ $todo->getCreatedAt()->format('Y年m月d日 H:i') }}
                                            </div>
                                        </div>

                                        <!-- Status Badge -->
                                        @if ($todo->getStatus()->isDone())
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                完了
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                未完了
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="ml-4 flex space-x-2">
                                        <a href="{{ route('todos.edit', $todo->getId()->getValue()) }}" 
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                            編集
                                        </a>
                                        <form action="{{ route('todos.destroy', $todo->getId()->getValue()) }}" method="POST" 
                                              onsubmit="return confirm('本当に削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
