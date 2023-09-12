<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\TypeAlerte;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TypeAlerteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(
            User::factory()->create(['email' => 'admin@admin.com'])
        );

        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function it_displays_index_view_with_type_alertes(): void
    {
        $typeAlertes = TypeAlerte::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('type-alertes.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.type_alertes.index')
            ->assertViewHas('typeAlertes');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_type_alerte(): void
    {
        $response = $this->get(route('type-alertes.create'));

        $response->assertOk()->assertViewIs('app.type_alertes.create');
    }

    /**
     * @test
     */
    public function it_stores_the_type_alerte(): void
    {
        $data = TypeAlerte::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('type-alertes.store'), $data);

        $this->assertDatabaseHas('type_alertes', $data);

        $typeAlerte = TypeAlerte::latest('id')->first();

        $response->assertRedirect(route('type-alertes.edit', $typeAlerte));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_type_alerte(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();

        $response = $this->get(route('type-alertes.show', $typeAlerte));

        $response
            ->assertOk()
            ->assertViewIs('app.type_alertes.show')
            ->assertViewHas('typeAlerte');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_type_alerte(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();

        $response = $this->get(route('type-alertes.edit', $typeAlerte));

        $response
            ->assertOk()
            ->assertViewIs('app.type_alertes.edit')
            ->assertViewHas('typeAlerte');
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

        $response = $this->put(
            route('type-alertes.update', $typeAlerte),
            $data
        );

        $data['id'] = $typeAlerte->id;

        $this->assertDatabaseHas('type_alertes', $data);

        $response->assertRedirect(route('type-alertes.edit', $typeAlerte));
    }

    /**
     * @test
     */
    public function it_deletes_the_type_alerte(): void
    {
        $typeAlerte = TypeAlerte::factory()->create();

        $response = $this->delete(route('type-alertes.destroy', $typeAlerte));

        $response->assertRedirect(route('type-alertes.index'));

        $this->assertModelMissing($typeAlerte);
    }
}
