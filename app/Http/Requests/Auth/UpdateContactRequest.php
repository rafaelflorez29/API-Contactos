<?php

namespace App\Http\Requests\Auth;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtenemos el contacto desde la ruta {contact}
        $contact = $this->route('contact');

        // Identificamos el ID del contacto (puede ser el modelo o el ID directamente)
        $contactId = $contact instanceof Contact ? $contact->id : $contact;

        return [
            'name' => ['required', 'string', 'max:150'],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('contacts', 'email')
                    ->where('user_id', $this->user()->id)
                    ->ignore($contactId),
            ],

            'number_phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('contacts', 'number_phone')
                    ->where('user_id', $this->user()->id)
                    ->ignore($contactId),
            ],

            'address' => ['nullable', 'string', 'max:150'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del contacto es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro de tus contactos.',
            'number_phone.required' => 'El número de teléfono es obligatorio.',
            'number_phone.unique' => 'Este número de teléfono ya pertenece a otro de tus contactos.',
        ];
    }
}
