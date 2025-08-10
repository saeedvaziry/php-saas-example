<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\RedirectResponse;

class LeaveTeamController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        /** @var ?TeamUser $user */
        $user = $team->users()
            ->where('user_id', user()->id)
            ->orWhere('email', user()->email)
            ->first();
        if (! $user) {
            abort(404);
        }

        $user->delete();

        return back()->with('success', __('You left the team successfully.'));
    }
}
