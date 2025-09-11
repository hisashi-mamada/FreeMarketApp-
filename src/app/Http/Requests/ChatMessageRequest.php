<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            // 画像が無いときは本文必須（= どちらか必須を片側だけで表現）
            'message' => 'nullable|required_without:image|string|max:400',
            // 画像は任意、来たら拡張子チェックのみ
            'image'   => 'nullable|mimes:jpeg,png',
        ];
    }

    public function attributes(): array
    {
        return [
            'message' => '本文',
            'image'   => '画像',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required_without' => '本文を入力してください',
            'message.max' => '本文は400文字以内で入力してください',
            'image.mimes' => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
