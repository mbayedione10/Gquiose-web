<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Role;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RoleUsersTest extends TestCase
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
    public function it_gets_role_users(): void
    {
        $role = Role::factory()->create();
        $users = User::factory()
            ->count(2)
            ->create([
                'role_id' => $role->id,
            ]);

        $response = $this->getJson(route('api.roles.users.index', $role));

        $response->assertOk()->assertSee($users[0]->name);
    }

    /**
     * @test
     */
    public function it_stores_the_role_users(): void
    {
        $role = Role::factory()->create();
        $data = User::factory()
            ->make([
                'role_id' => $role->id,
            ])
            ->toArray();
        $data['password'] = \Str::random('8');

        $response = $this->postJson(
            route('api.roles.users.store', $role),
            $data
        );

        unset($data['password']);
        unset($data['email_verified_at']);

        $this->assertDatabaseHas('users', $data);

        $response->assertStatus(201)->assertJsonFragment($data);

        $user = User::latest('id')->first();

        $this->assertEquals($role->id, $user->role_id);
    }
}
