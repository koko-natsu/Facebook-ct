<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostToTimeLineTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
    }


    /** @test */
    function a_user_can_post_a_text_post()
    {
        $this->actingAs($user = User::find(1), 'api');

        $response = $this->post('/api/posts', [
            'body' => 'Testing Body',
            'image' => 'image.jpg',
        ]);

        $post = Post::first();

        $this->assertCount(1, Post::all());
        $this->assertEquals($user->id, $post->user_id);
        $this->assertEquals('Testing Body', $post->body);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'type' => 'posts',
                    'post_id' => $post->id,
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
                        'body' => 'Testing Body',
                        'image' => 'image.jpg',
                        'posted_at' => $post->created_at->diffForHumans(),
                    ]
                ],
                'links' => [
                    'self' => url('/posts/'.$post->id),
                ]
            ]);
    }
}
