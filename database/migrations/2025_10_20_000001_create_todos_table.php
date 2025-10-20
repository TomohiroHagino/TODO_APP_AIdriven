<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->comment('タスクのタイトル');
            $table->boolean('is_done')->default(false)->comment('完了フラグ');
            $table->timestamp('created_at')->useCurrent()->comment('作成日時');
            
            // インデックス
            $table->index('is_done');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('todos');
    }
};

