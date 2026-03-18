<?php

use App\Models\Utilisateur;

it('generates full name from prenom and nom', function () {
    $user = new Utilisateur(['prenom' => 'Fatou', 'nom' => 'Diallo']);

    expect($user->name)->toBe('Fatou Diallo');
});

it('calculates age range for under 15', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 14;

    expect($user->dob)->toBe('-15 ans');
});

it('calculates age range for 15-17', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 16;

    expect($user->dob)->toBe('15-17 ans');
});

it('calculates age range for 18-24', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 20;

    expect($user->dob)->toBe('18-24 ans');
});

it('calculates age range for 25-29', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 27;

    expect($user->dob)->toBe('25-29 ans');
});

it('calculates age range for 30-35', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 32;

    expect($user->dob)->toBe('30-35 ans');
});

it('calculates age range for over 35', function () {
    $user = new Utilisateur();
    $user->anneedenaissance = now()->year - 40;

    expect($user->dob)->toBe('+35 ans');
});

it('hides password in serialization', function () {
    $user = new Utilisateur(['password' => 'secret']);

    expect(array_key_exists('password', $user->toArray()))->toBeFalse();
});
