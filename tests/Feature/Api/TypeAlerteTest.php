<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\TypeAlerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeAlerteTest extends TestCase
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
    public function it_gets_type_alertes_list(): void
    {
        $typeAlertes = TypeAlerte::factory()
            ->count(5)
            ->create();

        $response = $this->getJson(route('api.type-alertes.index'));

        $response->assertOk()->assertSee($typeAlertes[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_type_alerte(): void
    {
        $data = TypeAlerte::factory()
            ->make()
            ->toArray();

        $response = $this->postJson(route('api.type-alertes.store'), $data);

        $this->assertDatabaseHas('type_alertes', $data);

        $response->assertStatus(201)->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_updates_the_type_alerte(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->putJson(
            route('api.type-alertes.update', $typeAlerte),
            $data
        );

        $data['id'] = $typeAlerte->id;

        $this->assertDatabaseHas('type_alertes', $data);

        $response->assertOk()->assertJsonFragment($data);
    }

    /**
     * @test
     */
    public function it_deletes_the_type_alerte(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();

        $response = $this->deleteJson(
            route('api.type-alertes.destroy', $typeAlerte)
        );

        $this->assertModelMissing($typeAlerte);

        $response->assertNoContent();
    }
}
