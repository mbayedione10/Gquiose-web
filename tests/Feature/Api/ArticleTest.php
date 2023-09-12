<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Article;

use App\Models\Rubrique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArticleTest extends TestCase
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
    public function it_gets_articles_list(): void
    {
        $articles = Article::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.articles.index'));

        $response->assertOk()->assertSee($articles[0]->title);
    }

    /**
     * @test
     */
    public function it_stores_the_article(): void
    {
        $data = Article::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.articles.store'), $data);

        $this->assertDatabaseHas('articles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_article(): void
    {
        $article = Article::factory()->create();

        $rubrique = Rubrique::factory()->create();
        $user = User::factory()->create();

        $data = [
            'title' => $this->faker->sentence(10),
            'description' => $this->faker->sentence(15),
            'slug' => $this->faker->unique->slug(),
            'status' => $this->faker->boolean(),
            'video_url' => $this->faker->text(255),
            'audio_url' => $this->faker->text(255),
            'rubrique_id' => $rubrique->id,
            'user_id' => $user->id,
        ];

        $response = $this->putJson(
            route('api.articles.update', $article),
            $data
        );

        $data['id'] = $article->id;

        $this->assertDatabaseHas('articles', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_article(): void
    {
        $article = Article::factory()->create();

        $response = $this->deleteJson(route('api.articles.destroy', $article));

        $this->assertModelMissing($article);

        $response->assertNoContent();
    }
}
