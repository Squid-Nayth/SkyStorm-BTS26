<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $code,
        public int $minutes
    ) {
    }

    public function build(): static
    {
        return $this->subject('Code de reinitialisation du mot de passe')
            ->view('emails.password-reset-code');
    }
}
