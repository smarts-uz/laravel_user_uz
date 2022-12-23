<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $contact, $email)
    {
        $this->email_data = $data;
        $this->contact = $contact;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->email ?? $this->contact->email;
        return $this->from(env("MAIL_USERNAME"))
        ->to($email)
        ->subject("Email Verification")
        ->view('email.emailVerificationEmail', ['data'=>$this->email_data]);
    }
}
