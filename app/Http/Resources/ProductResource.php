<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "brand_id" => $this->brand_id,
            "category_id" => $this->category_id,
            "primary_image" => env("APP_FULL_URL") . $this->primary_image,
            "description" => $this->description,
            "price" => $this->description,
            "quantity" => $this->quantity,
            "delivery_amount" => $this->delivery_amount,
            "images" => ProductImageResource::collection($this->whenLoaded('images'))
        ];
    }
}
