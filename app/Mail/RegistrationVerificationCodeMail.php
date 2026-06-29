<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegistrationVerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $email,
        public string $code,
        public int $minutes
    ) {
    }

    public function build(): static
    {
        return $this->subject('Code de verification de votre compte')
            ->view('emails.registration-code');
    }
}
