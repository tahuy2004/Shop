<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSizeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:sizes,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên size là bắt buộc',
            'name.string' => 'Tên size phải là một chuỗi văn bản',
            'name.max' => 'Tên size không được dài quá 255 ký tự',
            'name.unique' => 'Tên size này đã tồn tại trong hệ thống',
        ];
    }
}
