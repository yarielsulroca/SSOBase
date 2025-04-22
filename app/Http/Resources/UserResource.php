<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'username' => $this->resource['username'],
            'email' => $this->resource['email'],
            'domain' => $this->resource['domain'],
            'name' => $this->resource['name'] ?? null,
            'displayName' => $this->resource['displayName'] ?? null,
        ];
    }
}
