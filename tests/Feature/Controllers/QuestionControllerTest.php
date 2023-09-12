<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Question;

use App\Models\Thematique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionControllerTest extends TestCase
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
    public function it_displays_index_view_with_questions(): void
    {
        $questions = Question::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('questions.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.questions.index')
            ->assertViewHas('questions');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_question(): void
    {
        $response = $this->get(route('questions.create'));

        $response->assertOk()->assertViewIs('app.questions.create');
    }

    /**
     * @test
     */
    public function it_stores_the_question(): void
    {
        $data = Question::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('questions.store'), $data);

        $this->assertDatabaseHas('questions', $data);

        $question = Question::latest('id')->first();

        $response->assertRedirect(route('questions.edit', $question));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_question(): void
    {
        $question = Question::factory()->create();

        $response = $this->get(route('questions.show', $question));

        $response
            ->assertOk()
            ->assertViewIs('app.questions.show')
            ->assertViewHas('question');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_question(): void
    {
        $question = Question::factory()->create();

        $response = $this->get(route('questions.edit', $question));

        $response
            ->assertOk()
            ->assertViewIs('app.questions.edit')
            ->assertViewHas('question');
    }

    /**
     * @test
     */
    public function it_updates_the_question(): void
    {
        $question = Question::factory()->create();

        $thematique = Thematique::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'reponse' => $this->faker->text(255),
            'option1' => $this->faker->text(255),
            'status' => $this->faker->boolean(),
            'thematique_id' => $thematique->id,
        ];

        $response = $this->put(route('questions.update', $question), $data);

        $data['id'] = $question->id;

        $this->assertDatabaseHas('questions', $data);

        $response->assertRedirect(route('questions.edit', $question));
    }

    /**
     * @test
     */
    public function it_deletes_the_question(): void
    {
        $question = Question::factory()->create();

        $response = $this->delete(route('questions.destroy', $question));

        $response->assertRedirect(route('questions.index'));

        $this->assertModelMissing($question);
    }
}
