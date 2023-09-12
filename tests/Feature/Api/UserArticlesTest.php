<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Article;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserArticlesTest extends TestCase
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
    public function it_gets_user_articles(): void
    {
        $user = User::factory()->create();
        $articles = Article::factory()
            ->count(2)
            ->create([
                'user_id' => $user->id,
            ]);

        $response = $this->getJson(route('api.users.articles.index', $user));

        $response->assertOk()->assertSee($articles[0]->title);
    }

    /**
     * @test
     */
    public function it_stores_the_user_articles(): void
    {
        $user = User::factory()->create();
        $data = Article::factory()
            ->make([
                'user_id' => $user->id,
            ])
            ->toArray();

        $response = $this->postJson(
            route('api.users.articles.store', $user),
            $data
        );

        $this->assertDatabaseHas('articles', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $article = Article::latest('id')->first();

        $this->assertEquals($user->id, $article->user_id);
    }
}
