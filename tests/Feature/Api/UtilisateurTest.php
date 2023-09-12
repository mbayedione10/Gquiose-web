<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Utilisateur;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilisateurTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_gets_utilisateurs_list(): void
    {
        $utilisateurs = Utilisateur::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.utilisateurs.index'));

        $response->assertOk()->assertSee($utilisateurs[0]->nom);
    }

    /**
     * @test
     */
    public function it_stores_the_utilisateur(): void
    {
        $data = Utilisateur::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.utilisateurs.store'), $data);

        $this->assertDatabaseHas('utilisateurs', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $data = [
            'nom' => $this->faker->text(255),
            'prenom' => $this->faker->text(255),
            'email' => $this->faker->unique->email(),
            'phone' => $this->faker->unique->phoneNumber(),
            'sexe' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->putJson(
            route('api.utilisateurs.update', $utilisateur),
            $data
        );

        $data['id'] = $utilisateur->id;

        $this->assertDatabaseHas('utilisateurs', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_utilisateur(): void
    {
        $utilisateur = Utilisateur::factory()->create();

        $response = $this->deleteJson(
            route('api.utilisateurs.destroy', $utilisateur)
        );

        $this->assertModelMissing($utilisateur);

        $response->assertNoContent();
    }
}
