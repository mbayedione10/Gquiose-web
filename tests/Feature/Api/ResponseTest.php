<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Response;

use App\Models\Question;
use App\Models\Utilisateur;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponseTest extends TestCase
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
    public function it_gets_responses_list(): void
    {
        $responses = Response::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.responses.index'));

        $response->assertOk()->assertSee($responses[0]->reponse);
    }

    /**
     * @test
     */
    public function it_stores_the_response(): void
    {
        $data = Response::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.responses.store'), $data);

        $this->assertDatabaseHas('responses', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_response(): void
    {
        $response = Response::factory()->create();

        $question = Question::factory()->create();
        $utilisateur = Utilisateur::factory()->create();

        $data = [
            'reponse' => $this->faker->text(255),
            'isValid' => $this->faker->boolean(),
            'question_id' => $question->id,
            'utilisateur_id' => $utilisateur->id,
        ];

        $response = $this->putJson(
            route('api.responses.update', $response),
            $data
        );

        $data['id'] = $response->id;

        $this->assertDatabaseHas('responses', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_response(): void
    {
        $response = Response::factory()->create();

        $response = $this->deleteJson(
            route('api.responses.destroy', $response)
        );

        $this->assertModelMissing($response);

        $response->assertNoContent();
    }
}
