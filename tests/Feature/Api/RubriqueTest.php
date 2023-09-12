<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Rubrique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RubriqueTest extends TestCase
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
    public function it_gets_rubriques_list(): void
    {
        $rubriques = Rubrique::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.rubriques.index'));

        $response->assertOk()->assertSee($rubriques[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_rubrique(): void
    {
        $data = Rubrique::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.rubriques.store'), $data);

        $this->assertDatabaseHas('rubriques', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_rubrique(): void
    {
        $rubrique = Rubrique::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->putJson(
            route('api.rubriques.update', $rubrique),
            $data
        );

        $data['id'] = $rubrique->id;

        $this->assertDatabaseHas('rubriques', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_rubrique(): void
    {
        $rubrique = Rubrique::factory()->create();

        $response = $this->deleteJson(
            route('api.rubriques.destroy', $rubrique)
        );

        $this->assertModelMissing($rubrique);

        $response->assertNoContent();
    }
}
