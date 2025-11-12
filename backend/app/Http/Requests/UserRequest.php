<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'name' => ['string', 'min:3', 'max:255'],
            'email' => ['email', Rule::unique('users')->ignore($this->user)],
            'contact' => ['string', 'regex:/^\(?\d{2}\)?\s?(?:9\d{4}|\d{4})-?\d{4}$/'],
            'password' => [
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(),
            ],
            'role' => [Rule::in(User::getRoles())],
            'image' => ['image', 'mimes:jpeg,png,jpg,webp'],
        ];

        if ($this->isMethod('post')) {
            $postRules = [
                'name' => ['required'],
                'email' => ['required'],
                'password' => ['required'],
                'role' => ['required']
            ];
        }

        if ($this->isMethod('put')) {
            $putRules = [
                'name' => ['sometimes'],
                'email' => ['sometimes'],
                'password' => ['sometimes'],
                'image' => ['sometimes'],
                'role' => ['sometimes']
            ];
        }

        return array_merge_recursive($rules, $postRules, $putRules);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string'   => 'O nome deve ser um texto.',
            'name.min'      => 'O nome deve ter pelo menos :min caracteres.',
            'name.max'      => 'O nome não pode ter mais que :max caracteres.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email'    => 'O e-mail informado não é válido.',
            'email.unique'   => 'Este e-mail já está cadastrado.',

            'contact.string' => 'O contato deve ser um texto.',
            'contact.regex'  => 'O número de contato deve estar no formato válido. Exemplo: (99) 99999-9999.',

            'password.required'    => 'A senha é obrigatória.',
            'password.min'         => 'A senha deve ter no mínimo :min caracteres.',
            'password.letters'     => 'A senha deve conter pelo menos uma letra.',
            'password.mixed_case'  => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers'     => 'A senha deve conter pelo menos um número.',
            'password.symbols'     => 'A senha deve conter pelo menos um símbolo.',

            'role.required' => 'O tipo de usuário é obrigatório.',
            'role.in'       => 'O tipo de usuário deve ser admin ou user.',

            'image.image' => 'A imagem enviada deve ser um arquivo de imagem válido.',
            'image.mimes' => 'A imagem deve estar nos formatos: :values.',
        ];
    }


}
