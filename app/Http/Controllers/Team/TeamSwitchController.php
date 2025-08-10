<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;

class TeamSwitchController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        $this->authorize('view', $team);

        user()->update([
            'current_team_id' => $team->id,
        ]);

        return redirect()->route('dashboard');
    }
}
