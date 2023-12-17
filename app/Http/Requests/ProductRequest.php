<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'category_id' => ['required', 'array'],
            'category_id.*' => ['exists:categories,id'],
            'product_name' => ['required'],
            'product_desc' => ['nullable'],
            'product_release_date' => ['nullable', 'date'],
            'product_acquisition_date' => ['required', 'date'],
            'product_qty' => ['required', 'numeric', 'min:1'],
            'product_acquisition_cost' => ['required', 'numeric', 'min:100'],
        ];
    }
}
