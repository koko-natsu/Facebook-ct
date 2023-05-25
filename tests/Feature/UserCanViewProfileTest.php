<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Post;

class UserCanViewProfileTest extends TestCase
{
    use RefreshDatabase;

    public function setup(): void
    {   
        parent::setUp();

        $this->withoutExceptionHandling();

        User::factory()->create();
    }


    /** @test */
    function a_user_can_view_user_profiles()
    {
        $this->actingAs($user = User::find(1), 'api');
        $posts = Post::factory(2)->create(['user_id' => $user->id]);

        $response = $this->get('/api/users/' . $user->id);

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
                    'self' => url('/users/' . $user->id),
                ]
            ]);
    }


    /** @test */
    function a_user_fetch_posts_for_a_profile()
    {
        $this->actingAs($user = User::find(1), 'api');
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->get('/api/users/' . $user->id . '/posts');

        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'post_id' => $post->id,
                            'attributes' => [
                                'body' => $post->body,
                                'image' => $post->image,
                                'posted_at' => $post->created_at->diffForHumans(),
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
                                ]
                            ]
                        ],
                        'links' => [
                            'self' => url('/posts/' . $post->id)
                        ]
                    ]
                ],
                'links' => [
                    'self' => url('/posts'),
                ]
            ]);
    }
}
