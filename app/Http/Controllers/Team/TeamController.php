<?php

namespace App\Http\Controllers\Team;

use App\Actions\Team\CreateTeam;
use App\Actions\Team\DeleteTeam;
use App\Actions\Team\UpdateTeam;
use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Http\Resources\TeamUserResource;
use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Team::class);

        return Inertia::render('teams/index', [
            'teams' => TeamResource::collection(
                user()
                    ->allTeams()
                    ->with(['owner', 'users'])
                    ->simplePaginate(20)
            ),
            'invitations' => TeamUserResource::collection(
                TeamUser::query()
                    ->where('email', user()->email)
                    ->whereNull('user_id')
                    ->simplePaginate(20)
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Team::class);

        app(CreateTeam::class)->create(user(), $request->input());

        return redirect()->route('teams.index')
            ->with('success', __('Team created successfully.'));
    }

    public function update(Team $team, Request $request): RedirectResponse
    {
        $this->authorize('update', $team);

        app(UpdateTeam::class)->update($team, $request->input());

        return back()->with('success', __('Team updated successfully.'));
    }

    public function destroy(Request $request, Team $team): RedirectResponse
    {
        $this->authorize('delete', $team);

        app(DeleteTeam::class)->delete(user(), $team, $request->input());

        return redirect()->route('teams.index')
            ->with('success', __('Team deleted successfully.'));
    }
}
