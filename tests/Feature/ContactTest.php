<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Laravel\Sanctum\Sanctum;

it('no se aceptan contactos con el mismo correo electrónico', function () {
    $user = User::factory()->create();
    Contact::factory()->create([
        'user_id' => $user->id,
        'email' => 'correoduplicado@error.com',
    ]);

    Sanctum::actingAs($user);
    expect(fn () => Contact::factory()->create([
        'user_id' => $user->id,
        'email' => 'correoduplicado@error.com',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

it('no se aceptan contactos con el mismo numero de telefono', function () {
    $user = User::factory()->create();
    Contact::factory()->create([
        'user_id' => $user->id,
        'number_phone' => '3000000000',
    ]);

    Sanctum::actingAs($user);
    expect(fn () => Contact::factory()->create([
        'user_id' => $user->id,
        'number_phone' => '3000000000',
    ]))->toThrow(UniqueConstraintViolationException::class);
});

it('rechaza un número de teléfono ya asignado a otro contacto', function () {
    $user = User::factory()->create();
    Contact::factory()->for($user)->create([
        'number_phone' => '3000000000',
    ]);

    Sanctum::actingAs($user);

    $this->postJson('/api/v1/contact', [
        'name' => 'Nuevo contacto',
        'email' => 'nuevo@example.test',
        'number_phone' => '3000000000',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['number_phone'])
        ->assertJsonPath('errors.number_phone.0', 'Este número ya le pertenece a otro contacto.');

    $this->assertDatabaseCount('contacts', 1);
});

it('un contacto puede ser editado', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create([
        'name' => 'rafa',
        'email' => 'rafa@gmail.com',
        'number_phone' => '3000000000',
        'avatar' => 'imagen.png',
        'address' => 'calle 27',
    ]);

    $actualizar = [
        'name' => 'Rafael',
        'email' => 'rafa@gmail.com',
        'number_phone' => '3000000001',
        'avatar' => 'perfil.png',
        'address' => 'Calle 14',
    ];

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->putJson("api/v1/contact/{$contact->id}", $actualizar);

    $resultado->assertStatus(200)
        ->assertJson([
            'message' => 'Contacto actualizado exitosamente.',
        ]);

    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'name' => 'Rafael',
        'number_phone' => '3000000001',
    ]);
});

it('un contacto puede ser eliminado por un usuario', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create([
        'name' => 'Rafael',
        'email' => 'rafa@gmail.com',
        'number_phone' => '3000000001',
        'avatar' => 'perfil.png',
        'address' => 'Calle 14',
    ]);

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->deleteJson("api/v1/contact/{$contact->id}");

    $resultado->assertStatus(204);
});

it('listar contactos', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create([
        'user_id' => $user->id,
    ]);

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->getJson('api/v1/contacts');

    $resultado->assertStatus(200)->assertJsonFragment([
        'id' => $contact->id,
        'name' => $contact->name,
        'email' => $contact->email,
        'address' => $contact->address,
        'number_phone' => $contact->number_phone,
        'avatar' => $contact->avatar,
        'user_id' => $contact->user_id,
    ]);
});

it('crea un contacto válido', function () {
    $user = User::factory()->create();

    $contact = [
        'user_id' => 1,
        'name' => 'Rafa',
        'email' => 'raafa@gmail.com',
        'number_phone' => '1234567890',
        'address' => 'Calle 12 #01-10',
        'avatar' => 'default.png',
    ];

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->postJson('api/v1/contact', $contact);

    $resultado->assertCreated();

});

it('devuelve 404 para un contacto ajeno', function () {
    $user = User::factory()->create();
    $visitante = User::factory()->create();
    $contact = Contact::factory()->for($user)->create([
        'name' => 'Contacto privado',
    ]);

    Sanctum::actingAs($visitante);
    $resultado = $this->actingAs($visitante)
        ->getJson('api/v1/contact/'.$contact->id)
        ->assertNotFound()->assertJsonMissing(['name' => 'Contacto privado']);
});
