<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'type' => 'comments',
                'comment_id' => $this->id,
                'attributes' => [
                    'body' => $this->body,
                    'commented_by' => new User($this->user),
                    'commented_at' => $this->created_at->diffForHumans(),
                ]
            ],
            'links' => [
                'self' => url('/posts/'.$this->post_id),
            ]
        ];
    }
}
