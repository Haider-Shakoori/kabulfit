<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $translation = $this->translation();

        return [
            'slug' => $translation?->slug,
            'name' => $translation?->name,
            'description' => $translation?->description,
        ];
    }
}
