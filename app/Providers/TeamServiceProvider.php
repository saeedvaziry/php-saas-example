<?php

namespace App\Providers;

use App\Http\Resources\TeamResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class TeamServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Inertia::share('team_provider', function (Request $request) {
            /** @var ?User $user */
            $user = $request->user();

            if (! $user) {
                return [
                    'current' => null,
                    'list' => [],
                ];
            }

            return [
                'current' => TeamResource::make($user->currentTeam()),
                'list' => TeamResource::collection($user->allTeams()->get()),
            ];
        });
    }
}
