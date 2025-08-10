<?php

use App\Enums\TeamRole;
use App\Mail\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can invite others', function () {
    Mail::fake();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    // make sure the user has default team
    $team = $user->currentTeam();

    $this
        ->from(route('teams.index'))
        ->post(route('teams.users.store', ['team' => $team]), [
            'email' => 'new-user@example.com',
            'role' => TeamRole::ADMIN->value,
        ])
        ->assertRedirect(route('teams.index'))
        ->assertSessionDoesntHaveErrors()
        ->assertSessionHas('success');

    $this->assertDatabaseHas('team_user', [
        'team_id' => $team->id,
        'email' => 'new-user@example.com',
    ]);

    Mail::assertSent(TeamInvitation::class);
});

test('can remove registered user from team', function () {
    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $team = $user->currentTeam();

    /** @var User $newUser */
    $newUser = User::factory()->create();

    $team->users()->create([
        'team_id' => $team->id,
        'user_id' => $newUser->id,
        'role' => TeamRole::VIEWER->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->delete(route('teams.users.destroy', ['team' => $team, 'email' => $newUser->email]))
        ->assertRedirect(route('teams.index'))
        ->assertSessionDoesntHaveErrors()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('team_user', [
        'team_id' => $team->id,
        'user_id' => $newUser->id,
    ]);
});

test('can remove owner from team', function () {
    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $team = $user->currentTeam();

    $this
        ->from(route('teams.index'))
        ->delete(route('teams.users.destroy', ['team' => $team, 'email' => $user->email]))
        ->assertSessionHas([
            'error' => __('You cannot remove the team owner.'),
        ]);
});

test('can remove invited user from team', function () {
    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $team = $user->currentTeam();

    $team->users()->create([
        'team_id' => $team->id,
        'email' => 'new-user@example.com',
        'role' => TeamRole::VIEWER->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->delete(route('teams.users.destroy', ['team' => $team, 'email' => 'new-user@example.com']))
        ->assertRedirect(route('teams.index'))
        ->assertSessionDoesntHaveErrors()
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('team_user', [
        'team_id' => $team->id,
        'email' => 'new-user@example.com',
    ]);
});

test('user can accept invitation', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $ownerTeam->users()->create([
        'email' => $user->email,
        'role' => TeamRole::VIEWER->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->get(route('teams.invitations.accept', ['team' => $ownerTeam]))
        ->assertRedirect(route('teams.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('team_user', [
        'team_id' => $ownerTeam->id,
        'user_id' => $user->id,
    ]);
});

test('user cannot join without invitation', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $this
        ->from(route('teams.index'))
        ->get(route('teams.invitations.accept', ['team' => $ownerTeam]))
        ->assertNotFound();

    $this->assertDatabaseMissing('team_user', [
        'team_id' => $ownerTeam->id,
        'user_id' => $user->id,
    ]);
});

test('user can leave team', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $ownerTeam->users()->create([
        'email' => $user->email,
        'role' => TeamRole::VIEWER->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->delete(route('teams.leave', ['team' => $ownerTeam]))
        ->assertRedirect(route('teams.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('team_user', [
        'team_id' => $ownerTeam->id,
        'email' => $user->email,
    ]);
});

test('user can leave team that is not invited', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $this
        ->from(route('teams.index'))
        ->delete(route('teams.leave', ['team' => $ownerTeam]))
        ->assertNotFound();
});
