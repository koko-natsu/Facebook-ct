<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Friend;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class RetrievePostsTest extends TestCase
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
    function a_user_can_retrieve_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($this->user, 'api');
        Friend::create([
            'user_id' => $this->user->id,
            'friend_id' => $this->anotherUser->id,
            'confirmed_at' => now(),
            'status' => 1,
        ]);

        $posts = Post::factory(2)->create(['user_id' => $this->anotherUser->id]);

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->last()->id,
                            'attributes' => [
                                'posted_by' => [
                                    'data' => [
                                        'type' => 'users',
                                        'user_id' => $this->anotherUser->id,
                                        'attributes' => [
                                            'name' => $this->anotherUser->name,
                                        ]
                                    ],
                                    'links' => [
                                        'self' => url('/users/' .$this->anotherUser->id)
                                    ]
                                ],
                                'body' => $posts->last()->body,
                                'image' => $posts->last()->image,
                                'posted_at' => $posts->last()->created_at->diffForHumans(),
                            ]
                        ],
                        'links' => [
                            'self' => url('/posts/' .$posts->last()->id),
                        ] 
                    ],
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->first()->id,
                            'attributes' => [
                                'posted_by' => [
                                    'data' => [
                                        'type' => 'users',
                                        'user_id' => $this->anotherUser->id,
                                        'attributes' => [
                                            'name' => $this->anotherUser->name,
                                        ]
                                    ],
                                    'links' => [
                                        'self' => url('/users/' .$this->anotherUser->id)
                                    ]
                                ],
                                'body' => $posts->first()->body,
                                'image' => $posts->first()->image,
                                'posted_at' => $posts->first()->created_at->diffForHumans(),
                            ]
                        ],
                        'links' => [
                            'self' => url('/posts/' .$posts->first()->id),
                        ] 
                    ]
                ],
                'links' => [
                    'self' => url('/posts'),
                ] 
            ]);
    }


    /** @test */
    function a_user_can_only_retrieve_their_posts()
    {
        $this->actingAs($this->user, 'api');
        $posts = Post::factory(2)->create([
            'user_id' => $this->anotherUser->id,
        ]);

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [],
                'links' => [
                    'self' => url('/posts'),
                ]
            ]);
    }
}
