<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Friend;
use Carbon\Carbon;

class FriendsTest extends TestCase
{
    use RefreshDatabase;

    public $user;
    public $anotherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->anotherUser = User::factory()->create();
    }

    /** @test */
    function a_user_can_send_a_friend_request()
    {
        $this->actingAs($this->user, 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => $this->anotherUser->id,
        ])->assertStatus(200);

        $friendRequest = Friend::first();

        $this->assertNotNull($friendRequest);
        $this->assertEquals($this->anotherUser->id, $friendRequest->friend_id);
        $this->assertEquals($this->user->id, $friendRequest->user_id);
        $response->assertExactJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => null,
                ]
            ],
            'links' => [
                'self' => url('/users/' .$this->anotherUser->id),
            ]
        ]);
    }


    /** @test */
    function only_valid_users_can_be_requested()
    {
        $this->actingAs($this->user, 'api');

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


    /** @test */
    function friend_requests_can_be_accepted()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->user, 'api');
        $this->post('/api/friend-request', [
            'friend_id' => $this->anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($this->anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $this->user->id,
                'status'  => 1,
            ])->assertStatus(200);

        $friendRequest = Friend::first();
        $this->assertNotNull($friendRequest->confirmed_at);
        $this->assertInstanceOf(Carbon::class, $friendRequest->confirmed_at);
        $this->assertEquals(now()->startOfSecond(), $friendRequest->confirmed_at);
        $this->assertEquals(1, $friendRequest->status);
        $response->assertExactJson([
            'data' => [
                'type' => 'friend-request',
                'friend_request_id' => $friendRequest->id,
                'attributes' => [
                    'confirmed_at' => $friendRequest->confirmed_at->diffForHumans(),
                ]
            ],
            'links' => [
                'self' => url('/users/' .$this->anotherUser->id),
            ]
        ]);
    }

    /** @test */
    function only_valid_friend_requests_can_be_accepted()
    {

        $response = $this->actingAs($this->anotherUser, 'api')
            ->post('/api/friend-request-response', [
                'user_id' => 123,
                'status'  => 1,
            ])->assertStatus(404);
        
        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);

        $response->assertExactJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found.',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }


    /** @test */
    function only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($this->user, 'api');
        $this->post('/api/friend-request', [
            'friend_id' => $this->anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(User::factory()->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => $this->user->id,
                'status'  => 1,
            ])->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);

        $response->assertExactJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found.',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }


    /** @test */
    function a_friend_id_is_required_for_friend_requests()
    {        
        $response = $this->actingAs($this->user, 'api');

        $response = $this->post('/api/friend-request', [
            'friend_id' => '',
        ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('friend_id', $responseString['errors']['meta']);
    }



    /** @test */
    function a_user_id_and_status_required_for_friend_request_response()
    {
        $response = $this->actingAs(User::factory()->create(), 'api')
            ->post('/api/friend-request-response', [
                'user_id' => '',
                'status'  => '',
            ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
        $this->assertArrayHasKey('status', $responseString['errors']['meta']);
    }


    /** @test */
    function a_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->user, 'api');

        $friendRequest = Friend::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/' .$this->anotherUser->id)
            ->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'type' => 'users',
                    'user_id' => $this->anotherUser->id,
                    'attributes' => [
                        'name' => $this->anotherUser->name,
                        'friendship' => [
                            'attributes' => [
                                'data' => [
                                    'type' => 'friend-request',
                                    'friend_request_id' => $friendRequest->id,
                                    'attributes' => [
                                        'confirmed_at' => '1 day ago',
                                    ]
                                ],
                                'links' => [
                                    'self' => url('/users/' .$this->anotherUser->id),
                                ]
                            ]
                        ]
                    ]
                ],
                'links' => [
                    'self' => url('/users/' .$this->anotherUser->id)
                ]
            ]);
    }


    /** @test */
    function a_inverse_friendship_is_retrieved_when_fetching_the_profile()
    {
        $this->withoutExceptionHandling();
        
        $this->actingAs($this->user, 'api');

        $friendRequest = Friend::create([
            'friend_id' => $this->user->id,
            'user_id' => $this->anotherUser->id,
            'confirmed_at' => now()->subDay(),
            'status' => 1,
        ]);

        $this->get('/api/users/' .$this->anotherUser->id)
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'type' => 'users',
                    'user_id' => $this->anotherUser->id,
                    'attributes' => [
                        'name' => $this->anotherUser->name,
                        'friendship' => [
                            'attributes' => [
                                'data' => [
                                    'type' => 'friend-request',
                                    'friend_request_id' => $friendRequest->id,
                                    'attributes' => [
                                        'confirmed_at' => '1 day ago',
                                    ]
                                ],
                                'links' => [
                                    'self' => url('/users/' .$this->user->id),
                                ]
                            ]
                        ]
                    ]
                ],
                'links' => [
                    'self' => url('/users/' .$this->anotherUser->id)
                ]
            ]);
    }


    /** @test */
    function friend_requests_can_be_ignored()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->user, 'api');

        $this->post('/api/friend-request', [
            'friend_id' => $this->anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs($this->anotherUser, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $this->user->id,
            ])->assertStatus(204);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest);
        $response->assertNoContent();
    }


    /** @test */
    function only_the_recipient_can_ignore_a_friend_request()
    {
        $this->actingAs($this->user, 'api');
        
        $this->post('/api/friend-request', [
            'friend_id' => $this->anotherUser->id,
        ])->assertStatus(200);

        $response = $this->actingAs(User::factory()->create(), 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => $this->user->id,
            ])->assertStatus(404);

        $friendRequest = Friend::first();
        $this->assertNull($friendRequest->confirmed_at);
        $this->assertNull($friendRequest->status);

        $response->assertExactJson([
            'errors' => [
                'code' => 404,
                'title' => 'Friend Request Not Found.',
                'detail' => 'Unable to locate the friend request with the given information.',
            ]
        ]);
    }


    /** @test */
    function a_user_id_required_for_friend_request_response()
    {
        $response = $this->actingAs($this->user, 'api')
            ->delete('/api/friend-request-response/delete', [
                'user_id' => '',
            ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('user_id', $responseString['errors']['meta']);
    }

}
