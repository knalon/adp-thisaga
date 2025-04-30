<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\Permission\Models\Permission;

class AuthUserResource extends JsonResource
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
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'name' => $this->name,
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'roles' => $this->getRoleNames(),
            'stripe_account_active' => (bool) $this->stripe_account_active,
        ];
    }
}
