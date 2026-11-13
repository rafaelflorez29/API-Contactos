<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;

function loginData(array $cambios = []): array
{
    return array_merge([
        'email' => 'ana@example.test',
        'password' => 'secreta123',
    ], $cambios);
}

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'ana@example.test',
        'password' => Hash::make('secreta123'),
    ]);

});

it('entrega un token con credenciales correctas', function () {
    $this->postJson('/api/v1/auth/login', loginData())
        ->assertOk()
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email']])
        ->assertJsonPath('user.email', 'ana@example.test')
        ->assertJsonMissingPath('user.password');

    expect($this->user->tokens()->count())->toBe(1);
    expect($this->user->tokens()->first()->name)->toBe('ana@example.test');
});

it('rechaza una contraseña incorrecta con 422', function () {
    $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra1234']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('usa el mismo mensaje si el correo no existe', function () {
    $this->postJson('/api/v1/auth/login', loginData(['email' => 'nadie@example.test']))
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Las credenciales no son correctas.');
});

it('valida los campos obligatorios', function (string $campo) {
    $this->postJson('/api/v1/auth/login', loginData([$campo => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$campo]);
})->with(['email', 'password']);

it('pide token para ver el perfil', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

it('responde 401 en JSON aunque falte la cabecera Accept', function () {
    $this->get('/api/v1/auth/me')
        ->assertUnauthorized()
        ->assertHeader('Content-Type', 'application/json');
});

it('devuelve el perfil del usuario autenticado', function () {
    Sanctum::actingAs($this->user);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'ana@example.test')
        ->assertJsonMissingPath('data.password');
});

it('cierra sesión y revoca el token', function () {
    $token = $this->user->createToken('prueba')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/auth/logout')
        ->assertNoContent();

    expect($this->user->tokens()->count())->toBe(0);
});

it('limita los intentos de login a 5 por minuto', function () {
    foreach (range(1, 5) as $intento) {
        $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra1234']))
            ->assertUnprocessable();
    }

    $this->postJson('/api/v1/auth/login', loginData(['password' => 'otra1234']))
        ->assertTooManyRequests();
});
