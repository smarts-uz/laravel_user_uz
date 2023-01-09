<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
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
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $data, string $subject = null)
    {
        $this->data = $data;

        if (!empty($subject))
            $this->subject = $subject;
        else
            $this->subject = __('Уведомление от ') . config('app.name');

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(env("MAIL_USERNAME"))
            ->subject($this->subject)
            ->view('email.emailVerificationEmail', ['data' => $this->data]);
    }
}
