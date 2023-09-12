<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Thematique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThematiqueTest extends TestCase
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
    public function it_gets_thematiques_list(): void
    {
        $thematiques = Thematique::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.thematiques.index'));

        $response->assertOk()->assertSee($thematiques[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_thematique(): void
    {
        $data = Thematique::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.thematiques.store'), $data);

        $this->assertDatabaseHas('thematiques', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->putJson(
            route('api.thematiques.update', $thematique),
            $data
        );

        $data['id'] = $thematique->id;

        $this->assertDatabaseHas('thematiques', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_thematique(): void
    {
        $thematique = Thematique::factory()->create();

        $response = $this->deleteJson(
            route('api.thematiques.destroy', $thematique)
        );

        $this->assertModelMissing($thematique);

        $response->assertNoContent();
    }
}
