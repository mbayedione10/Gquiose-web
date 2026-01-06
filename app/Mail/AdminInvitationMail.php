<?php

namespace App\Mail;

use App\Models\AdminInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public AdminInvitation $invitation;
    public string $activationUrl;

    public function __construct(AdminInvitation $invitation)
    {
        $this->invitation = $invitation;
        $this->activationUrl = $invitation->getActivationUrl();
    }

    public function build()
    {
        return $this
            ->subject('Invitation à rejoindre GquiOse - Administration')
            ->markdown('email.admin-invitation');
    }
}
