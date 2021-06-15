<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'caption' => $this->caption ?? null,
            'image' => asset($this->image),
            'comments' => !$this->comments->isEmpty() ? CommentResource::collection($this->comments) : null,
        ];
    }
}
