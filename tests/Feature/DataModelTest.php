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
