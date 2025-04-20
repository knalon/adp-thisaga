<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VendorUserResource;

class OrderViewResource extends JsonResource
{
    public static $wrap = 'false';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'total-price' => $this->total_price,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'vendorUser' => new VendorUserResource($this->vendorUser),
            'orderItems' => $this->orderItems->map(fn ($item) => [
                    'id' => $orderItem->id,
                    'quantity' => $orderItem->quantity,
                    'price' => $orderItem->price,
                    'variation_type_option_ids' => $orderItem->variation_type_option_ids,
                    'product' => [
                        'id' => [
                            'id' => $item->product->id,
                            'title' => $item->product->title,
                            'slug' => $item->product->slug,
                            'description' => $item->product->description,
                            'image' => $item->product->getImageForOptions($item->variation_type_option_ids ?: []),
                        ],
                ])
        ];
    }
}
