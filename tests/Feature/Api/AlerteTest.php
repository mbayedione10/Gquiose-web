<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Alerte;

use App\Models\Ville;
use App\Models\TypeAlerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AlerteTest extends TestCase
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
    public function it_gets_alertes_list(): void
    {
        $alertes = Alerte::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.alertes.index'));

        $response->assertOk()->assertSee($alertes[0]->ref);
    }

    /**
     * @test
     */
    public function it_stores_the_alerte(): void
    {
        $data = Alerte::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.alertes.store'), $data);

        $this->assertDatabaseHas('alertes', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $typeAlerte = TypeAlerte::factory()->create();
        $ville = Ville::factory()->create();

        $data = [
            'ref' => $this->faker->unique->text(255),
            'description' => $this->faker->sentence(15),
            'latitude' => $this->faker->latitude(),
            'longitude' => $this->faker->longitude(),
            'etat' => $this->faker->word(),
            'type_alerte_id' => $typeAlerte->id,
            'ville_id' => $ville->id,
        ];

        $response = $this->putJson(route('api.alertes.update', $alerte), $data);

        $data['id'] = $alerte->id;

        $this->assertDatabaseHas('alertes', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_alerte(): void
    {
        $alerte = Alerte::factory()->create();

        $response = $this->deleteJson(route('api.alertes.destroy', $alerte));

        $this->assertModelMissing($alerte);

        $response->assertNoContent();
    }
}
