<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Team\AcceptTeamInviteController;
use App\Http\Controllers\Team\LeaveTeamController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Team\TeamSwitchController;
use App\Http\Controllers\Team\TeamUserController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('/settings')->middleware(['auth'])->group(function () {
    Route::redirect('/', '/settings/profile')->name('settings');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('teams', TeamController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('/teams/{team}/switch', TeamSwitchController::class)->name('teams.switch');
    Route::post('/teams/{team}/users', [TeamUserController::class, 'store'])->name('teams.users.store');
    Route::delete('/teams/{team}/users/{email}', [TeamUserController::class, 'destroy'])
        ->name('teams.users.destroy');
    Route::delete('/teams/{team}/leave', LeaveTeamController::class)->name('teams.leave');
    Route::get('/teams/{team}/invitations/accept', AcceptTeamInviteController::class)
        ->name('teams.invitations.accept');
    Route::resource('tokens', TokenController::class)->only(['index', 'store', 'destroy']);
});
