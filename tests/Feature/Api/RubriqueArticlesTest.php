<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Article;
use App\Models\Rubrique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RubriqueArticlesTest extends TestCase
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
    public function it_gets_rubrique_articles(): void
    {
        $rubrique = Rubrique::factory()->create();
        $articles = Article::factory()
            ->count(2)
            ->create([
                'rubrique_id' => $rubrique->id,
            ]);

        $response = $this->getJson(
            route('api.rubriques.articles.index', $rubrique)
        );

        $response->assertOk()->assertSee($articles[0]->title);
    }

    /**
     * @test
     */
    public function it_stores_the_rubrique_articles(): void
    {
        $rubrique = Rubrique::factory()->create();
        $data = Article::factory()
            ->make([
                'rubrique_id' => $rubrique->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.rubriques.articles.store', $rubrique),
            $data
        );

        $this->assertDatabaseHas('articles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $article = Article::latest('id')->first();

        $this->assertEquals($rubrique->id, $article->rubrique_id);
    }
}
