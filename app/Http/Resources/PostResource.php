<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'body' => $this->body,
            'image' =>$this->image_path ? Storage::url($this->image_path) : 'data:image/png;base64,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',
            'is_published' => $this->is_published,
            'published_at' => $this->published_at,
            'user' => UserResource::make($this->whenLoaded('user')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at'=> $this->created_at,
            'updated_at'=> $this->updated_at,
        ];
    }
}
