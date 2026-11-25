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
        'user_id' => $user->id,
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
        ->putJson("/api/v1/contact/update/{$contact->id}", $actualizar);

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
        'user_id' => $user->id,
        'name' => 'Rafael',
        'email' => 'rafa@gmail.com',
        'number_phone' => '3000000001',
        'avatar' => 'perfil.png',
        'address' => 'Calle 14',
    ]);

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->deleteJson("/api/v1/contact/destroy/{$contact->id}");

    $resultado->assertStatus(204);
});

it('mostrar usuarios', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->getJson('/api/v1/user/'.$user->id);

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
    $resultado = $this->actingAs($user)->postJson('/api/v1/user/created', $datos);

    $resultado->assertStatus(201)->assertJson([
        'message' => 'Usuario creado con exito',
    ]);
});

it('rechaza usuarios con contraseñas de menos de ocho caracteres', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this->actingAs($user)->postJson('/api/v1/user/created', [
        'name' => 'Usuario de prueba',
        'email' => 'prueba@example.test',
        'password' => '1234567',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['password']);

    $this->assertDatabaseMissing('users', ['email' => 'prueba@example.test']);
});

it('un usuario puede ser editado', function () {
    $user = User::factory()->create([
        'name' => 'rafa',
        'email' => 'rafael@gmail.com',
        'password' => 'rafaelflorez',
    ]);

    $actualizar = [
        'name' => 'rafa',
        'email' => 'rafael1@gmail.com',
        'password' => 'rafaelfg',
    ];

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->putJson("/api/v1/user/update/{$user->id}", $actualizar);

    $resultado->assertStatus(200)->assertJson([
        'message' => 'Usuario actualizado exitosamente.',
    ]);
});

it('eliminar usuario', function(){
    $user = User::factory()->create();

    Sanctum::actingAs($user);
    $resultado = $this->actingAs($user)->deleteJson("/api/v1/user/destroy/{$user->id}");

    $resultado->assertStatus(204);
});

it('impedir que un usuario edite un contacto que no le pertenece', function(){
    $dueño = User::factory()->create();

    $atacante = User::factory()->create();

    $contact = Contact::factory()->create(['user_id' => $dueño->id]);
    Sanctum::actingAs($atacante);

    $resultado = $this->actingAs($atacante)->putJson("/api/v1/contact/update/{$contact->id}", [
        'user_id' => $dueño->id,
        "name" => "Hack",
        "email" => "hack@gmail.com",
        "number_phone" => "12345678"
    ]);

    $resultado->assertStatus(404);
});

it('impedir que un usuario elimine un contacto que no le pertenece', function(){
    $dueño = User::factory()->create();
    $atacante = User::factory()->create();

    $contact = Contact::factory()->create(['user_id' => $dueño->id]);
    Sanctum::actingAs($atacante);
    $this->deleteJson("/api/v1/contact/destroy/{$contact->id}")->assertNotFound();

    $this->assertDatabaseHas("contacts", ["id" => $contact->id]);
});