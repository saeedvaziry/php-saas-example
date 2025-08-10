<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can see teams and roles', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $team = $user->currentTeam();

    /** @var User $otherUser */
    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam();
    $otherTeam->users()->create([
        'user_id' => $user->id,
        'role' => TeamRole::ADMIN->value,
    ]);

    $this->get(route('teams.index'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('teams/index')
            ->where('teams.data.0.id', $team->id)
            ->where('teams.data.0.role', TeamRole::OWNER->value)
            ->where('teams.data.1.id', $otherTeam->id)
            ->where('teams.data.1.role', TeamRole::ADMIN->value)
        );
});

test('user has current team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('dashboard'))->assertSuccessful();

    $this->assertDatabaseHas('teams', [
        'owner_id' => $user->id,
        'name' => 'Default Team',
    ]);
});

test('make invited team as current if user doesnt have any', function () {
    /** @var User $user */
    $user = User::factory()->create();

    // Ensure the user a current team
    $team = $user->currentTeam();

    $this->actingAs($user);

    /** @var User $otherUser */
    $otherUser = User::factory()->create();
    $otherTeam = $otherUser->currentTeam();

    $user->current_team_id = $otherTeam->id;
    $user->save();

    $this->get(route('dashboard'))->assertSuccessful();

    expect($user->refresh()->current_team_id)->toEqual($team->id);
});

test('user can create team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->post(route('teams.store'), [
        'name' => 'New Team',
    ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', [
        'owner_id' => $user->id,
        'name' => 'New Team',
    ]);
});

test('user can view teams', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $this->get(route('teams.index'))
        ->assertSuccessful();
});

test('user can switch team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Test Team']);

    $this->put(route('teams.switch', ['team' => $team->id]))
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('dashboard'));

    expect($user->refresh()->current_team_id)->toEqual($team->id);
});

test('user can update team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Old Team Name']);

    $this
        ->from(route('teams.index'))
        ->put(route('teams.update', ['team' => $team->id]), [
            'name' => 'Updated Team Name',
        ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Updated Team Name',
    ]);
});

test('team update name must be unique', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $user->currentTeam()->update(['name' => 'Default Team']);

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Old Team Name']);

    $this
        ->from(route('teams.index'))
        ->put(route('teams.update', ['team' => $team->id]), [
            'name' => 'Default Team',
        ])
        ->assertSessionHasErrors('name');
});

test('user can delete team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    // make sure the user has a current team
    $user->currentTeam();

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Team to Delete']);

    $this->delete(route('teams.destroy', ['team' => $team->id]), [
        'name' => 'Team to Delete',
    ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseMissing('teams', [
        'id' => $team->id,
    ]);
});

test('user cannot delete default team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    $team = $user->currentTeam();

    $user->ownedTeams()->create(['name' => 'last team']);

    $this->delete(route('teams.destroy', ['team' => $team]), [
        'name' => $team->name,
    ])
        ->assertSessionHasErrors([
            'name' => 'Cannot delete your current team.',
        ]);

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
    ]);
});

test('user cannot delete last team', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Team to Delete']);

    $this->delete(route('teams.destroy', ['team' => $team]))
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('teams', [
        'id' => $user->currentTeam()->id,
    ]);
});

test('team deletion validation fails', function () {
    /** @var User $user */
    $user = User::factory()->create();

    $this->actingAs($user);

    /** @var Team $team */
    $team = $user->ownedTeams()->create(['name' => 'Team to Delete']);

    $this->delete(route('teams.destroy', ['team' => $team->id]), [
        'name' => 'wrong-name',
    ])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
    ]);
});

test('cannot delete not owned team', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $ownerTeam->users()->create([
        'user_id' => $user->id,
        'role' => TeamRole::ADMIN->value,
    ]);

    $this->delete(route('teams.destroy', ['team' => $ownerTeam]), [
        'name' => $ownerTeam->name,
    ])
        ->assertForbidden();
});

test('can edit team as admin role', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $ownerTeam->users()->create([
        'user_id' => $user->id,
        'role' => TeamRole::ADMIN->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->put(route('teams.update', ['team' => $ownerTeam]), [
            'name' => 'new-name',
        ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('teams.index'));

    $this->assertDatabaseHas('teams', [
        'id' => $ownerTeam->id,
        'name' => 'new-name',
    ]);
});

test('cannot edit team as viewer role', function () {
    /** @var User $owner */
    $owner = User::factory()->create();
    $ownerTeam = $owner->currentTeam();

    /** @var User $user */
    $this->actingAs($user = User::factory()->create());

    $ownerTeam->users()->create([
        'user_id' => $user->id,
        'role' => TeamRole::VIEWER->value,
    ]);

    $this
        ->from(route('teams.index'))
        ->put(route('teams.update', ['team' => $ownerTeam]), [
            'name' => 'new-name',
        ])
        ->assertForbidden();
});
