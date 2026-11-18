<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Override;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        $user = $this->route('user');

        $userId = $user instanceof User ? $user->id : $user;

        return [
            'name' => ['string', 'max:100', 'min:1'],
            'email' => ['email', 'max:150', 'min:10', Rule::unique('users', 'email')->where('id', $this->user()->id)],
            'password' => ['string', 'max:15', 'min:8'],
        ];
    }

    #[Override]
    public function messages()
    {
        return [
            'name.min' => 'El nombre debe contener al menos 1 caracter.',
            'name.max' => 'El nombre debe contener maximo 100 catacteres.',
            'email.email' => 'El correo debe contener el formato de email.',
            'email.min' => 'El correo debe ser de al menos 10 caracteres.',
            'email.max' => 'El correo debe almacenar como maximo 150 caracteres.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro de tus contactos o usuarios.',
            'password.min' => 'La contraseña debe ser de al menos 8 caracteres.',
            'password.max' => 'La contraseña no debe tener mas de 15 caracteres.',
        ];
    }
}
