<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PriceHistoryRequest extends FormRequest
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
        $postRules = [];
        $putRules = [];

        $rules = [
            'product_id' => ['uuid', 'exists:products,id'],
            'price' => ['numeric', 'min:0', 'max:9999999999'],
            'checked_at' => ['date']
        ];

        if($this->isMethod('post')) {
            $postRules = [
                'product_id' => ['required'],
                'price' => ['required'],
                'checked_at' => ['required'],
            ];
        }

        if($this->isMethod('put')) {
            $putRules = [
                'product_id' => ['sometimes'],
                'price' => ['sometimes'],
                'checked_at' => ['sometimes'],
            ];
        }

        return array_merge_recursive($rules, $postRules, $putRules);
    }
}
