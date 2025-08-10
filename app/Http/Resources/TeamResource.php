<?php

namespace App\Http\Resources;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Team */
class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var ?User $user */
        $user = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $user ? $this->role($user)?->value : null,
            'owner' => TeamUserResource::make(new TeamUser([
                'team_id' => $this->id,
                'user_id' => $this->owner->id,
                'email' => $this->owner->email,
                'role' => TeamRole::OWNER,
                'type' => 'user',
            ])),
            'is_current' => $user->current_team_id === $this->id,
            'users' => TeamUserResource::collection($this->whenLoaded('users')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
