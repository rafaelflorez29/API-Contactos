<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

it('no se aceptan contactos con el mismo correo electrónico', function () {
    $user = User::factory()->create();
    Contact::factory()->create([
        'user_id' => $user->id,
        'email' => 'correoduplicado@error.com',
    ]);

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

    expect(fn () => Contact::factory()->create([
        'user_id' => $user->id,
        'number_phone' => '3000000000',
    ]))->toThrow(UniqueConstraintViolationException::class);
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

    $resultado = $this->actingAs($user)
        ->putJson("api/v1/contact/{$contact->id}", $actualizar);

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

    $resultado = $this->actingAs($user)->deleteJson("api/v1/contact/{$contact->id}");

    $resultado->assertStatus(200)->assertJson([
        'message' => 'Contacto eliminado exitosamente.',
    ]);
});

it('listar contactos', function() {
    $user = User::factory()->create();

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $resultado = $this->actingAs($user)->getJson('api/v1/contacts');

    $resultado->assertStatus(200)->assertJsonFragment([
        "id" => $contact->id,
        "name" => $contact->name,
        "email" => $contact->email,
        "address" => $contact->address,
        "number_phone" => $contact->number_phone,
        "avatar" => $contact->avatar,
        "user_id" => $contact->user_id,
    ]);
});
