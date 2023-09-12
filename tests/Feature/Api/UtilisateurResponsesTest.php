<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Response;
use App\Models\Utilisateur;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilisateurResponsesTest extends TestCase
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
    public function it_gets_utilisateur_responses(): void
    {
        $utilisateur = Utilisateur::factory()->create();
        $responses = Response::factory()
            ->count(2)
            ->create([
                'utilisateur_id' => $utilisateur->id,
            ]);

        $response = $this->getJson(
            route('api.utilisateurs.responses.index', $utilisateur)
        );

        $response->assertOk()->assertSee($responses[0]->reponse);
    }

    /**
     * @test
     */
    public function it_stores_the_utilisateur_responses(): void
    {
        $utilisateur = Utilisateur::factory()->create();
        $data = Response::factory()
            ->make([
                'utilisateur_id' => $utilisateur->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.utilisateurs.responses.store', $utilisateur),
            $data
        );

        $this->assertDatabaseHas('responses', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $response = Response::latest('id')->first();

        $this->assertEquals($utilisateur->id, $response->utilisateur_id);
    }
}
