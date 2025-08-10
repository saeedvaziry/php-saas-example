<?php

use App\Mail\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('team invitation mailable builds correctly', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $team = $user->currentTeam();

    $mailable = new TeamInvitation($team);
    $built = $mailable->build();

    // Assert
    expect($built->subject)->toEqual(__('Team Invitation'));

    $viewData = $built->buildViewData();

    expect($viewData['acceptUrl'])->toEqual(route('teams.invitations.accept', ['team' => $team]));

    expect($built->markdown)->toEqual('emails.team-invitation');
});

test('team invitation can be sent', function () {
    // Arrange
    Mail::fake();

    /** @var User $user */
    $user = User::factory()->create();
    $team = $user->currentTeam();

    // Act
    Mail::to('test@example.com')->send(new TeamInvitation($team));

    // Assert
    Mail::assertSent(TeamInvitation::class, function (TeamInvitation $mail) use ($team) {
        return $mail->team->is($team);
    });
});
