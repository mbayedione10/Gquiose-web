<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Suivi;

use App\Models\Alerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuiviTest extends TestCase
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
    public function it_gets_suivis_list(): void
    {
        $suivis = Suivi::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.suivis.index'));

        $response->assertOk()->assertSee($suivis[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_suivi(): void
    {
        $data = Suivi::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.suivis.store'), $data);

        $this->assertDatabaseHas('suivis', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $alerte = Alerte::factory()->create();

        $data = [
            'name' => $this->faker->name(),
            'observation' => $this->faker->sentence(15),
            'alerte_id' => $alerte->id,
        ];

        $response = $this->putJson(route('api.suivis.update', $suivi), $data);

        $data['id'] = $suivi->id;

        $this->assertDatabaseHas('suivis', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_suivi(): void
    {
        $suivi = Suivi::factory()->create();

        $response = $this->deleteJson(route('api.suivis.destroy', $suivi));

        $this->assertModelMissing($suivi);

        $response->assertNoContent();
    }
}
