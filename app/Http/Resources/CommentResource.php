<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => (int) $this->id,
            'post_id' => (int) $this->post_id,
            'user' => $this->user ? new UserResource($this->user) : null,
            'body' => $this->body,
        ];
    }
}
