<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DeleteTeam
{
    public function delete(User $user, Team $team, array $input): void
    {
        Validator::make($input, [
            'name' => 'required',
        ])->validate();

        if ($user->ownedTeams()->count() === 1) {
            throw ValidationException::withMessages([
                'name' => __('Cannot delete the last team.'),
            ]);
        }

        if ($user->current_team_id == $team->id) {
            throw ValidationException::withMessages([
                'name' => __('Cannot delete your current team.'),
            ]);
        }

        $team->delete();
    }
}
