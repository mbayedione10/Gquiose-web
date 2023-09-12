<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Question;

use App\Models\Thematique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionTest extends TestCase
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
    public function it_gets_questions_list(): void
    {
        $questions = Question::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.questions.index'));

        $response->assertOk()->assertSee($questions[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_question(): void
    {
        $data = Question::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.questions.store'), $data);

        $this->assertDatabaseHas('questions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
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

        $response = $this->putJson(
            route('api.questions.update', $question),
            $data
        );

        $data['id'] = $question->id;

        $this->assertDatabaseHas('questions', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_question(): void
    {
        $question = Question::factory()->create();

        $response = $this->deleteJson(
            route('api.questions.destroy', $question)
        );

        $this->assertModelMissing($question);

        $response->assertNoContent();
    }
}
