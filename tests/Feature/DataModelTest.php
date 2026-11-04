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

it('un usuario puede actualizar un contacto', function () {
    $user = User::factory()->create();
    
    // Creamos un contacto inicial
    $contact = Contact::factory()->create([
        'name' => 'Nombre Antiguo',
        'email' => 'ana@example.com',
    ]);

    $payload = [
        'name' => 'Nombre Actualizado',
        'email' => 'ana@example.com', // Mantiene su propio email sin dar error de duplicado
        'phone' => '999888777',
    ];

    $this->actingAs($user)
         ->putJson("/api/contacts/{$contact->id}", $payload)
         ->assertStatus(200)
         ->assertJsonPath('data.name', 'Nombre Actualizado');

    // Verificamos que cambió en la base de datos
    $this->assertDatabaseHas('contacts', [
        'id' => $contact->id,
        'name' => 'Nombre Actualizado'
    ]);
});