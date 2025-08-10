<?php

namespace App\Http\Controllers\Team;

use App\Actions\Team\InviteToTeam;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamUserController extends Controller
{
    public function store(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('update', $team);

        app(InviteToTeam::class)->invite($team, $request->input());

        return back()->with('success', __('An invitation has been sent to the email address.'));
    }

    public function destroy(Team $team, string $email): RedirectResponse
    {
        $this->authorize('update', $team);

        /** @var ?User $user */
        $user = User::query()->where('email', $email)->first();

        if ($user && $user->is($team->owner)) {
            return back()->with('error', __('You cannot remove the team owner.'));
        }

        $team->users()
            ->where('user_id', $user?->id)
            ->orWhere('email', $email)
            ->delete();

        return back()->with('success', __('The user has been removed.'));
    }
}
