<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\RedirectResponse;

class AcceptTeamInviteController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        /** @var ?TeamUser $user */
        $user = $team->users()->where('email', user()->email)->first();
        if (! $user) {
            abort(404);
        }

        $user->email = null;
        $user->user_id = user()->id;
        $user->save();

        return redirect()->route('teams.index')->with('success', __('You joined the team successfully.'));
    }
}
