<?php

namespace App\Mail;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public Team $team;

    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    public function build(): static
    {
        return $this
            ->markdown('emails.team-invitation', [
                'acceptUrl' => route('teams.invitations.accept', ['team' => $this->team]),
            ])
            ->subject(__('Team Invitation'));
    }
}
