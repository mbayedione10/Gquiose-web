<?php

use App\Models\Utilisateur;

it('can login with email and password', function () {
    $user = Utilisateur::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'test@example.com',
        'password' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['utilisateur', 'token', 'token_type', 'expires_in'],
        ]);
});

it('can login with phone and password', function () {
    $user = Utilisateur::factory()->create([
        'phone' => '+224623456789',
        'password' => bcrypt('password123'),
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => '+224623456789',
        'password' => 'password123',
        'platform' => 'ios',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.token_type', 'Bearer');
});

it('rejects login with wrong password', function () {
    Utilisateur::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'test@example.com',
        'password' => 'wrongpassword',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 400);
});

it('rejects login for inactive account', function () {
    Utilisateur::factory()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password123'),
        'status' => false,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'inactive@example.com',
        'password' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 403);
});

it('validates login request fields', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422);
});

it('can register by phone', function () {
    $response = $this->postJson('/api/v1/register', [
        'phone' => '+224623000001',
        'sexe' => 'F',
        'anneedenaissance' => 2000,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('utilisateurs', ['phone' => '+224623000001']);
});

it('can register by email', function () {
    $response = $this->postJson('/api/v1/register', [
        'email' => 'new@example.com',
        'sexe' => 'F',
        'anneedenaissance' => 2000,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('utilisateurs', ['email' => 'new@example.com']);
});

it('rejects registration for minors under 13', function () {
    $response = $this->postJson('/api/v1/register', [
        'phone' => '+224623000002',
        'sexe' => 'F',
        'anneedenaissance' => now()->year - 10,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 400);
});

it('rejects duplicate phone registration', function () {
    Utilisateur::factory()->create(['phone' => '+224623000003']);

    $response = $this->postJson('/api/v1/register', [
        'phone' => '+224623000003',
        'sexe' => 'F',
        'anneedenaissance' => 2000,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    // Should not create a second user with same phone
    $this->assertDatabaseCount('utilisateurs', 1);
});

it('can logout with valid token', function () {
    $user = Utilisateur::factory()->create(['status' => true]);
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer $token",
    ])->postJson('/api/v1/logout');

    $response->assertStatus(200);
});

it('can get profile when authenticated', function () {
    $user = Utilisateur::factory()->create(['status' => true]);

    $response = $this->actingAs($user)
        ->getJson('/api/v1/profile');

    $response->assertStatus(200);
});

it('cannot access profile without authentication', function () {
    $response = $this->getJson('/api/v1/profile');

    $response->assertStatus(401);
});
