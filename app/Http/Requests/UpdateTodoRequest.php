<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * タスク更新リクエスト
 * 
 * バリデーションルール:
 * - title: 必須、文字列、最大255文字
 */
class UpdateTodoRequest extends FormRequest
{
    /**
     * このリクエストを実行する権限があるかを判定
     */
    public function authorize(): bool
    {
        // 認証不要のため常にtrue
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
        ];
    }

    /**
     * カスタムエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
        ];
    }

    /**
     * バリデーション済みのタイトルを取得
     */
    public function getTitle(): string
    {
        return $this->validated()['title'];
    }
}
