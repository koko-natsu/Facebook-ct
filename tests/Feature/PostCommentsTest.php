<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCommentsTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    function a_user_can_comment_on_a_post()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123, 'user_id' => $user->id]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'A great comment here.',
        ])->assertStatus(200);

        $comment = Comment::first();

        $this->assertCount(1, Comment::all());
        $this->assertEquals($user->id, $comment->user_id);
        $this->assertEquals($post->id, $comment->post_id);
        $this->assertEquals('A great comment here.', $comment->body);

        $response->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'comments',
                        'comment_id' => 1,
                        'attributes' => [
                            'body' => 'A great comment here.',
                            'commented_by' => [
                                'data' => [
                                    'user_id' => $user->id,
                                    'attributes' => [
                                        'name' => $user->name,
                                    ]
                                ]
                            ],
                            'commented_at' => $comment->created_at->diffForHumans(),
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/'.$post->id),
                    ]
                ]
            ]
        ]);
    }


    /** @test */
    function a_body_is_required_to_leave_a_comment_on_a_post()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123, 'user_id' => $user->id]);

        $response = $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => '',
        ])->assertStatus(422);

        $responseString = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('body', $responseString['errors']['meta']);
    }


    /** @test */
    function post_are_returned_with_comments()
    {
        $this->actingAs($user = User::factory()->create(), 'api');
        $post = Post::factory()->create(['id' => 123, 'user_id' => $user->id]);
        
        $this->post('/api/posts/'.$post->id.'/comment', [
            'body' => 'A great comment here.',
        ])->assertStatus(200);

        $comment = Comment::first();

        $this->get('/api/posts')
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'data' => [
                            'type' => 'posts',
                            'attributes' => [
                                'comments' => [
                                    'data' => [
                                        [
                                            'data' => [
                                                'type' => 'comments',
                                                'comment_id' => $comment->id,
                                                'attributes' => [
                                                    'body' => $comment->body,
                                                    'commented_by' => [
                                                        'data' => [
                                                            'user_id' => $user->id,
                                                            'attributes' => [
                                                                'name' => $user->name,
                                                            ]
                                                        ]
                                                    ],
                                                    'commented_at' => $comment->created_at->diffForHumans(),
                                                ]
                                            ],
                                            'links' => [
                                                'self' => url('/posts/'.$post->id),
                                            ]
                                        ]
                                    ],
                                    'comment_count' => 1,
                                ]
                            ]
                        ]
                    ]
                ]
            ]);
    }

}