<?php

use App\Models\Utilisateur;

/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

it('returns token and user data on successful email login', function () {
    Utilisateur::factory()->create([
        'email' => 'fatou@gquiose.test',
        'password' => bcrypt('password123'),
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'fatou@gquiose.test',
        'password' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.utilisateur.email', 'fatou@gquiose.test')
        ->assertJsonStructure(['data' => ['utilisateur', 'token', 'token_type', 'expires_in']]);
});

it('returns token on successful phone login', function () {
    Utilisateur::factory()->create([
        'phone' => '+224623111111',
        'password' => bcrypt('password123'),
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => '+224623111111',
        'password' => 'password123',
        'platform' => 'ios',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.utilisateur.phone', '+224623111111');
});

it('rejects wrong password with code 400', function () {
    Utilisateur::factory()->create([
        'email' => 'test@gquiose.test',
        'password' => bcrypt('correct-password'),
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'test@gquiose.test',
        'password' => 'wrong-password',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 400);
});

it('rejects inactive account with code 403', function () {
    Utilisateur::factory()->create([
        'email' => 'inactive@gquiose.test',
        'password' => bcrypt('password123'),
        'status' => false,
    ]);

    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'inactive@gquiose.test',
        'password' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 403);
});

it('returns 422 when login fields are missing', function () {
    $response = $this->postJson('/api/v1/login', []);

    $response->assertStatus(422);
});

it('returns 422 for unsupported platform', function () {
    $response = $this->postJson('/api/v1/login', [
        'identifier' => 'test@gquiose.test',
        'password' => 'password123',
        'platform' => 'windows',
    ]);

    $response->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| Registration (edge cases that return before SMS is called)
|--------------------------------------------------------------------------
*/

it('rejects registration for users under 13 with code 400', function () {
    $response = $this->postJson('/api/v1/register', [
        'phone' => '+224623000099',
        'sexe' => 'F',
        'anneedenaissance' => now()->year - 10,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 400);
    $this->assertDatabaseMissing('utilisateurs', ['phone' => '+224623000099']);
});

it('rejects duplicate phone for confirmed account with code 409', function () {
    Utilisateur::factory()->create([
        'phone' => '+224623000088',
        'status' => true,
    ]);

    $response = $this->postJson('/api/v1/register', [
        'phone' => '+224623000088',
        'sexe' => 'F',
        'anneedenaissance' => 2000,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertStatus(409)
        ->assertJsonPath('code', 409);
});

it('rejects registration without phone or email with code 400', function () {
    $response = $this->postJson('/api/v1/register', [
        'sexe' => 'F',
        'anneedenaissance' => 2000,
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'platform' => 'android',
    ]);

    $response->assertJsonPath('code', 400);
});

/*
|--------------------------------------------------------------------------
| Logout & Profile
|--------------------------------------------------------------------------
*/

it('returns 200 on logout with valid token', function () {
    $user = Utilisateur::factory()->create(['status' => true]);
    $token = $user->createToken('auth_token')->plainTextToken;

    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->postJson('/api/v1/logout');

    $response->assertStatus(200);
});

it('returns profile data for authenticated user', function () {
    $user = Utilisateur::factory()->create([
        'email' => 'profile@gquiose.test',
        'status' => true,
    ]);

    $response = $this->actingAs($user)->getJson('/api/v1/profile');

    $response->assertStatus(200)
        ->assertJsonPath('data.utilisateur.email', 'profile@gquiose.test')
        ->assertJsonStructure(['data' => ['utilisateur' => ['id', 'email', 'sexe', 'anneedenaissance']]]);
});

it('returns 401 when accessing profile without token', function () {
    $response = $this->getJson('/api/v1/profile');

    $response->assertStatus(401);
});
