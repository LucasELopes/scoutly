<?php

namespace App\Http\Requests;

use App\Models\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubscriptionRequest extends FormRequest
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
            'plan' => [Rule::in(Subscription::getPlans()), 'nullable'],
            'active_until' => ['date']
        ];

        if($this->isMethod('post')) {
            $postRules = [
                'user_id' => ['required'],
                'active_until' => ['required']
            ];
        }

        if($this->isMethod('put')) {
            $putRules = [
                'user_id' => 'sometimes',
                'plan' => 'sometimes',
                'active_until' => 'sometimes'
            ];
        }

        return array_merge_recursive($rules, $postRules, $putRules);
    }


    public function messages(): array
    {
        return [
            'user_id.required' => 'O campo usuário é obrigatório.',
            'user_id.uuid' => 'O identificador do usuário deve ser um UUID válido.',
            'user_id.exists' => 'O usuário informado não foi encontrado.',

            'plan.in' => 'O plano selecionado é inválido.',

            'active_until.required' => 'A data de validade é obrigatória.',
            'active_until.date' => 'A data de validade deve ser uma data válida.',
        ];
    }

}
