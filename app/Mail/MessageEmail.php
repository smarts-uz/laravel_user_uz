<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

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
            ->view('email.messageEmail', ['data' => $this->data]);
    }
}
