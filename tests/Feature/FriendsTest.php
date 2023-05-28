<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Friend;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
    }

    /** @test */
    function a_user_can_send_a_friend_request()
    {
        $this->withoutExceptionHandling();

        $user = User::find(1);
        $this->actingAs($user, 'api');

       $anotherUser = User::factory()->create();

       $response = $this->post('/api/friend-request', [
            'friend_id' => $anotherUser->id,
       ])->assertStatus(200);

       $friendRequest = Friend::first();

       $this->assertNotNull($friendRequest);
       $this->assertEquals($anotherUser->id, $friendRequest->friend_id);
       $this->assertEquals($user->id, $friendRequest->user_id);
       $response->assertExactJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url('/users/' .$anotherUser->id),
            ]
        ]);
    }


    /** @test */
    function only_valid_users_can_be_requested()
    {
        $user = User::find(1);
        $this->actingAs($user, 'api');

       $response = $this->post('/api/friend-request', [
            'friend_id' => 123,
       ])->assertStatus(404);

       $this->assertNull(Friend::first());
       $response->assertExactJson([
            'errors' => [
                'code' => 404,
                'title' => 'User Not Found.',
                'detail' => 'Unable to locate the user with the given information.',
            ]
       ]);
    }
}
