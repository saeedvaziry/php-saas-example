<?php

namespace App\Actions\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateTeam
{
    public function create(User $user, array $input): Team
    {
        $this->validate($user, $input);

        $team = new Team([
            'owner_id' => $user->id,
            'name' => $input['name'],
        ]);
        $team->save();

        $user->current_team_id = $team->id;
        $user->save();

        return $team;
    }

    private function validate(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => [
                'required',
                Rule::unique('teams')->where('owner_id', $user->id),
            ],
        ])->validate();
    }
}
