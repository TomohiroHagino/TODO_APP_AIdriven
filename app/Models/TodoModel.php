<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Todoテーブルに対応するEloquentモデル
 */
class TodoModel extends Model
{
    /**
     * テーブル名
     */
    protected $table = 'todos';

    /**
     * タイムスタンプの自動管理を無効化
     * (created_atのみ使用)
     */
    public $timestamps = false;

    /**
     * 複数代入可能な属性
     */
    protected $fillable = [
        'title',
        'is_done',
        'created_at',
    ];

    /**
     * キャストする属性
     */
    protected $casts = [
        'is_done' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];
}

