<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Models\Rubrique;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RubriqueControllerTest extends TestCase
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
    public function it_displays_index_view_with_rubriques(): void
    {
        $rubriques = Rubrique::factory()
            ->count(5)
            ->create();

        $response = $this->get(route('rubriques.index'));

        $response
            ->assertOk()
            ->assertViewIs('app.rubriques.index')
            ->assertViewHas('rubriques');
    }

    /**
     * @test
     */
    public function it_displays_create_view_for_rubrique(): void
    {
        $response = $this->get(route('rubriques.create'));

        $response->assertOk()->assertViewIs('app.rubriques.create');
    }

    /**
     * @test
     */
    public function it_stores_the_rubrique(): void
    {
        $data = Rubrique::factory()
            ->make()
            ->toArray();

        $response = $this->post(route('rubriques.store'), $data);

        $this->assertDatabaseHas('rubriques', $data);

        $rubrique = Rubrique::latest('id')->first();

        $response->assertRedirect(route('rubriques.edit', $rubrique));
    }

    /**
     * @test
     */
    public function it_displays_show_view_for_rubrique(): void
    {
        $rubrique = Rubrique::factory()->create();

        $response = $this->get(route('rubriques.show', $rubrique));

        $response
            ->assertOk()
            ->assertViewIs('app.rubriques.show')
            ->assertViewHas('rubrique');
    }

    /**
     * @test
     */
    public function it_displays_edit_view_for_rubrique(): void
    {
        $rubrique = Rubrique::factory()->create();

        $response = $this->get(route('rubriques.edit', $rubrique));

        $response
            ->assertOk()
            ->assertViewIs('app.rubriques.edit')
            ->assertViewHas('rubrique');
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

        $response = $this->put(route('rubriques.update', $rubrique), $data);

        $data['id'] = $rubrique->id;

        $this->assertDatabaseHas('rubriques', $data);

        $response->assertRedirect(route('rubriques.edit', $rubrique));
    }

    /**
     * @test
     */
    public function it_deletes_the_rubrique(): void
    {
        $rubrique = Rubrique::factory()->create();

        $response = $this->delete(route('rubriques.destroy', $rubrique));

        $response->assertRedirect(route('rubriques.index'));

        $this->assertModelMissing($rubrique);
    }
}
