<?php

namespace App\Traits;

use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<int, Team> $ownedTeams
 * @property Collection<int, Team> $invitedTeams
 */
trait HasTeams
{
    public function currentTeam(): Team
    {
        /** @var ?Team $team */
        $team = Team::query()->find($this->current_team_id);

        if (! $team || (! $this->isOwnerOfTeam($team) && ! $this->isUserOfTeam($team))) {
            $team = $this->ownedTeams()->first();
        }

        if (! $team) {
            $team = new Team([
                'name' => 'Default Team',
                'owner_id' => $this->id,
            ]);
            $team->save();
        }

        /** @var Team $team */
        if ($this->current_team_id !== $team->id) {
            $this->current_team_id = $team->id;
            $this->save();
        }

        return $team;
    }

    public function ownedTeams(): HasMany
    {
        return $this->hasMany(Team::class, 'owner_id');
    }

    public function allTeams(): Builder
    {
        return Team::query()
            ->where('owner_id', $this->id)
            ->orWhereHas('users', fn (Builder $q) => $q->where('user_id', $this->id));
    }

    public function isUserOfTeam(?Team $team): bool
    {
        return $team && $team->users()->where('user_id', $this->id)->exists();
    }

    public function isOwnerOfTeam(?Team $team): bool
    {
        return $team?->owner_id === $this->id;
    }

    public function hasRolesInTeam(Team $team, array $roles): bool
    {
        if ($this->isOwnerOfTeam($team)) {
            return true;
        }

        return $team->users()
            ->where('user_id', $this->id)
            ->whereIn('role', $roles)
            ->exists();
    }
}
