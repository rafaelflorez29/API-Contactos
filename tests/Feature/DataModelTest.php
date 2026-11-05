<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;

it('crear usuarios por defecto', function () {
    $user = User::factory()->create();

    expect($user->fresh())->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeNull()
        ->and($user->email)->not->toBeNull();
});

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

it('mostrar usuarios', function () {});
