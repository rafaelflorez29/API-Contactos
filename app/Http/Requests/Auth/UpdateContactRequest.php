<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        // Obtenemos el contacto desde la ruta (Route Model Binding)
        $contact = $this->route('contact');

        return [
            'name' => ['required', 'string', 'max:255'],
            
            // Ignoramos el ID del contacto actual para que permita mantener su propio email
            'email' => [
                'required', 
                'email:rfc,dns', 
                'max:255', 
                'unique:contacts,email,' . $contact->id
            ],
            
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del contacto es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Este correo electrónico ya pertenece a otro contacto.',
        ];
    }
}