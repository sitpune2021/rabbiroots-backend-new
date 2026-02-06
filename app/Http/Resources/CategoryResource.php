<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'slug'          => $this->slug,
            'level'         => $this->level,

            'parent' => $this->whenLoaded('parent', function () {
                return [
                    'id'   => $this->parent->id,
                    'name' => $this->parent->name,
                ];
            }),

            'images' => [
                'icon'  => $this->icon ? asset('storage/'.$this->icon) : null,
                'image' => $this->image ? asset('storage/'.$this->image) : null,
                'og'    => $this->og_image ? asset('storage/'.$this->og_image) : null,
            ],

            'seo' => [
                'meta_title'       => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords'    => $this->meta_keywords,
                'canonical_url'    => $this->canonical_url,
                'is_indexable'     => $this->is_indexable,
            ],

            'sort_order' => $this->sort_order,
            'is_active'  => (bool) $this->is_active,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
        // return parent::toArray($request);
    }
}
