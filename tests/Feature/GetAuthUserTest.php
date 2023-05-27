<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class GetAuthUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        User::factory()->create(); 
    }

    /** @test */
    function authenticated_user_cen_be_fetched()
    {
        $user = User::find(1);
        $this->actingAs($user, 'api');

        $response = $this->get('/api/auth-user');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'type' => 'users',
                    'user_id' => $user->id,
                    'attributes' => [
                        'name' => $user->name,
                    ]
                ],
                'links' => [
                    'self' => url('/users/' .$user->id),
                ]
            ]);
    }


}
