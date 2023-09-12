<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Question;
use App\Models\Response;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionResponsesTest extends TestCase
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
    public function it_gets_question_responses(): void
    {
        $question = Question::factory()->create();
        $responses = Response::factory()
            ->count(2)
            ->create([
                'question_id' => $question->id,
            ]);

        $response = $this->getJson(
            route('api.questions.responses.index', $question)
        );

        $response->assertOk()->assertSee($responses[0]->reponse);
    }

    /**
     * @test
     */
    public function it_stores_the_question_responses(): void
    {
        $question = Question::factory()->create();
        $data = Response::factory()
            ->make([
                'question_id' => $question->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.questions.responses.store', $question),
            $data
        );

        $this->assertDatabaseHas('responses', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $response = Response::latest('id')->first();

        $this->assertEquals($question->id, $response->question_id);
    }
}
