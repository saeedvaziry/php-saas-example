<?php

namespace App\Http\Resources;

use App\Models\TeamUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TeamUser
 */
class TeamUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'team_id' => $this->team_id,
            'team_name' => $this->team->name ?? null,
            'email' => $this->email ?? $this->user->email,
            'role' => $this->role,
            'type' => $this->user_id ? 'user' : 'invitation',
        ];
    }
}
