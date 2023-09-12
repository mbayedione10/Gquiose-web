<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Ville;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VilleControllerTest extends TestCase
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
    public function it_displays_index_view_with_villes(): void
    {
        $villes = Ville::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('villes.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.villes.index')
            ->assertViewHas('villes');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_ville(): void
    {
        $response = $this->get(route('villes.create'));

        $response->assertOk()->assertViewIs('app.villes.create');
    }

    /**
     * @test
     */
    public function it_stores_the_ville(): void
    {
        $data = Ville::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('villes.store'), $data);

        $this->assertDatabaseHas('villes', $data);

        $ville = Ville::latest('id')->first();

        $response->assertRedirect(route('villes.edit', $ville));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_ville(): void
    {
        $ville = Ville::factory()->create();

        $response = $this->get(route('villes.show', $ville));

        $response
            ->assertOk()
            ->assertViewIs('app.villes.show')
            ->assertViewHas('ville');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_ville(): void
    {
        $ville = Ville::factory()->create();

        $response = $this->get(route('villes.edit', $ville));

        $response
            ->assertOk()
            ->assertViewIs('app.villes.edit')
            ->assertViewHas('ville');
    }

    /**
     * @test
     */
    public function it_updates_the_ville(): void
    {
        $ville = Ville::factory()->create();

        $data = [
            'name' => $this->faker->unique->name(),
            'status' => $this->faker->boolean(),
        ];

        $response = $this->put(route('villes.update', $ville), $data);

        $data['id'] = $ville->id;

        $this->assertDatabaseHas('villes', $data);

        $response->assertRedirect(route('villes.edit', $ville));
    }

    /**
     * @test
     */
    public function it_deletes_the_ville(): void
    {
        $ville = Ville::factory()->create();

        $response = $this->delete(route('villes.destroy', $ville));

        $response->assertRedirect(route('villes.index'));

        $this->assertModelMissing($ville);
    }
}
