<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Response;

use App\Models\Question;
use App\Models\Utilisateur;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponseControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_responses(): void
    {
        $responses = Response::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('responses.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.responses.index')
            ->assertViewHas('responses');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_response(): void
    {
        $response = $this->get(route('responses.create'));

        $response->assertOk()->assertViewIs('app.responses.create');
    }

    /**
     * @test
     */
    public function it_stores_the_response(): void
    {
        $data = Response::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('responses.store'), $data);

        $this->assertDatabaseHas('responses', $data);

        $response = Response::latest('id')->first();

        $response->assertRedirect(route('responses.edit', $response));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_response(): void
    {
        $response = Response::factory()->create();

        $response = $this->get(route('responses.show', $response));

        $response
            ->assertOk()
            ->assertViewIs('app.responses.show')
            ->assertViewHas('response');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_response(): void
    {
        $response = Response::factory()->create();

        $response = $this->get(route('responses.edit', $response));

        $response
            ->assertOk()
            ->assertViewIs('app.responses.edit')
            ->assertViewHas('response');
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

        $response = $this->put(route('responses.update', $response), $data);

        $data['id'] = $response->id;

        $this->assertDatabaseHas('responses', $data);

        $response->assertRedirect(route('responses.edit', $response));
    }

    /**
     * @test
     */
    public function it_deletes_the_response(): void
    {
        $response = Response::factory()->create();

        $response = $this->delete(route('responses.destroy', $response));

        $response->assertRedirect(route('responses.index'));

        $this->assertModelMissing($response);
    }
}
