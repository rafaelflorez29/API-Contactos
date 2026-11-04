<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Almacena un nuevo contacto en la base de datos y redirige.
     */
    public function store(StoreContactRequest $request): RedirectResponse
    {
        // Creamos el contacto con los datos ya validados por el Form Request
        Contact::create($request->validated());

        // Redirigimos de vuelta con un mensaje flash de éxito
        return redirect()->route('contacts.index')
                         ->with('success', 'Contacto creado exitosamente.');
    }

    /**
     * Actualiza un contacto existente en la base de datos y redirige.
     */
    public function update(StoreContactRequest $request, Contact $contact): RedirectResponse
    {
        // Actualizamos el contacto con los datos ya validados por el Form Request
        $contact->update($request->validated());

        // Redirigimos de vuelta con un mensaje flash de éxito
        return redirect()->route('contacts.index')
                         ->with('success', 'Contacto actualizado exitosamente.');
    }

    /**
     * Elimina un contacto de la base de datos y redirige.
     */
    public function destroy(Contact $contact): RedirectResponse
    {
        // Eliminamos el contacto
        $contact->delete();

        // Redirigimos de vuelta con un mensaje flash de éxito
        return redirect()->route('contacts.index')
                         ->with('success', 'Contacto eliminado exitosamente.');
    }
}
