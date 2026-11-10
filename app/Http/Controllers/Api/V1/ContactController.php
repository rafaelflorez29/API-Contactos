<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreContactRequest;
use App\Http\Requests\Auth\UpdateContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Contacto creado exitosamente.',
            'data' => $contact,
        ], 201);
    }

    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->update($request->validated());

        return response()->json([
            'message' => 'Contacto actualizado exitosamente.',
            'data' => $contact,
        ], 200);
    }

    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado exitosamente.',
        ], 200);
    }
    public function index(){
        $contacts = auth()->user()->contacts()->get();

        return response()->json([
            "contact" => $contacts,
        ], 200);
    }
}
