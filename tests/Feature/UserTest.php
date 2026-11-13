<?php

use App\Models\Contact;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('crear usuarios por defecto', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);
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

    Sanctum::actingAs($user);
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

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->deleteJson("api/v1/contact/{$contact->id}");

    $resultado->assertStatus(204);
});

it('mostrar usuarios', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->getJson('api/v1/users');

    $resultado->assertStatus(200)->assertJsonStructure([
        'user' => [
            '*' => [
                'id',
                'name',
                'email',
            ],
        ],
    ]);
});

it('crear usuario', function () {
    $user = User::factory()->create();

    $datos = [
        'name' => 'Rafael Andrés Flórez González',
        'email' => 'rafael@gmail.com',
        'password' => 'rafael12',
    ];

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->postJson('/api/v1/created-user', $datos);

    $resultado->assertStatus(201)->assertJson([
        'message' => 'Usuario creado con exito',
    ]);
});

it('rechaza usuarios con contraseñas de menos de ocho caracteres', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user)->postJson('/api/v1/created-user', [
        'name' => 'Usuario de prueba',
        'email' => 'prueba@example.test',
        'password' => '1234567',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

    $this->assertDatabaseMissing('users', ['email' => 'prueba@example.test']);
});
