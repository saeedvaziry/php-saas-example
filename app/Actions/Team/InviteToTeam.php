<?php

namespace App\Actions\Team;

use App\Enums\TeamRole;
use App\Mail\TeamInvitation;
use App\Models\Team;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InviteToTeam
{
    public function invite(Team $team, array $input): void
    {
        $this->validate($team, $input);

        $team->users()->create([
            'email' => $input['email'],
            'role' => $input['role'],
        ]);

        Mail::to($input['email'])->send(new TeamInvitation($team));
    }

    protected function validate(Team $team, array $input): void
    {
        Validator::make($input, $this->rules($team), [
            'email.unique' => __('This user has already been invited to the team.'),
        ])->validate();
    }

    protected function rules(Team $team): array
    {
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('team_user')->where(function (Builder $query) use ($team) {
                    $query->where('team_id', $team->id);
                }),
                Rule::notIn([
                    ...$team->registeredUsers()->pluck('users.email'),
                    $team->owner->email,
                ]),
            ],
            'role' => [
                'required',
                Rule::in([
                    TeamRole::ADMIN,
                    TeamRole::VIEWER,
                ]),
            ],
        ];
    }
}
