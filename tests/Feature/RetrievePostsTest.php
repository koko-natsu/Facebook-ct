<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class RetrievePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_user_can_retrieve_posts()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = User::factory()->create(), 'api');
        $posts = Post::factory(2)->create(['user_id' => $user->id]);

        $response = $this->get('/api/posts');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $posts->last()->id,
                            'attributes' => [
                                'posted_by' => [
                                    'data' => [
                                        'type' => 'users',
                                        'user_id' => $user->id,
                                        'attributes' => [
                                            'name' => $user->name,
                                        ]
                                    ],
                                    'links' => [
                                        'self' => url('/users/' .$user->id)
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
                                        'user_id' => $user->id,
                                        'attributes' => [
                                            'name' => $user->name,
                                        ]
                                    ],
                                    'links' => [
                                        'self' => url('/users/' .$user->id)
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
        $this->actingAs($user = User::factory()->create(), 'api');
        $posts = Post::factory(2)->create();

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
