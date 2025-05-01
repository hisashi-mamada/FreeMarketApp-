<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
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
            'image' => ['nullable', 'image', 'mimes:jpeg,png'],
            'name' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }
}
