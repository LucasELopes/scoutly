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
        $postRules = [];
        $putRules = [];

        $rules = [
            'user_id' => ['uuid', 'exists:users,id'],
            'name' => ['string', 'max:100'],
            'url' => ['url'],
            'desired_price' => ['decimal:0,9999999999']
        ];

        if($this->isMethod('post')) {
            $postRules = [
                'user_id' => ['required'],
                'name' => ['required'],
                'url' => ['required'],
                'desired_price' => ['required']
            ];
        }

        if($this->isMethod('put')) {
            $putRules = [
                'user_id' => ['sometimes'],
                'name' => ['sometimes'],
                'url' => ['sometimes'],
                'desired_price' => ['sometimes']
            ];
        }

        return array_merge_recursive($rules, $postRules, $putRules);
    }
}
