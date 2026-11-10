<?php

use App\Models\User;
use App\Models\Contact;

it('crear usuarios por defecto', function () {
    $user = User::factory()->create();

    expect($user->fresh())->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeNull()
        ->and($user->email)->not->toBeNull();
});

it('un usuario puede editar un contacto', function () {
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

it('un usuario puede eliminar contactos', function () {
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

it('mostrar usuarios', function () {
    $user = User::factory()->create();

    $resultado = $this->actingAs($user)->getJson('api/v1/users');

    $resultado->assertStatus(200)->assertJsonStructure([
        "user" => [
            "*" => [
                "id",
                "name",
                "email",
            ]
        ]
    ]);
});

it('crear usuario', function () {
    $user = User::factory()->create();

    $datos = [
        'name' => 'Rafael Andrés Flórez González',
        'email' => 'rafael@gmail.com',
        'password' => 'rafael123',
    ];

    $resultado = $this->actingAs($user)->postJson('/api/v1/created-user', $datos);

    $resultado->assertStatus(201)->assertJson([
            'message' => 'Usuario creado con exito'
        ]);
});