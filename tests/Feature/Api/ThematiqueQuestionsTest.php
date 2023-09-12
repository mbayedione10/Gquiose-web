<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Question;
use App\Models\Thematique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThematiqueQuestionsTest extends TestCase
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
    public function it_gets_thematique_questions(): void
    {
        $thematique = Thematique::factory()->create();
        $questions = Question::factory()
            ->count(2)
            ->create([
                'thematique_id' => $thematique->id,
            ]);

        $response = $this->getJson(
            route('api.thematiques.questions.index', $thematique)
        );

        $response->assertOk()->assertSee($questions[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_thematique_questions(): void
    {
        $thematique = Thematique::factory()->create();
        $data = Question::factory()
            ->make([
                'thematique_id' => $thematique->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.thematiques.questions.store', $thematique),
            $data
        );

        $this->assertDatabaseHas('questions', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $question = Question::latest('id')->first();

        $this->assertEquals($thematique->id, $question->thematique_id);
    }
}
