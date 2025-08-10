<?php

namespace App\Actions\Team;

use App\Models\Team;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateTeam
{
    public function update(Team $team, array $input): Team
    {
        $this->validate($team, $input);

        $team->fill([
            'name' => $input['name'],
        ]);
        $team->save();

        return $team;
    }

    private function validate(Team $team, array $input): void
    {
        Validator::make($input, [
            'name' => [
                'required',
                Rule::unique('teams')->where('owner_id', $team->owner_id)->ignore($team->id),
            ],
        ])->validate();
    }
}
