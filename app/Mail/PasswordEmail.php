<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetEmailLink;
    // public $auto_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($resetEmailLink)
    {
        $this->reset_email_link = $resetEmailLink;
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.password-email')
                    ->subject('iLearn Africa - Password Reset Link!')
                    ->with([
                       'reset_link' => $this->reset_email_link
                    ]);
    }
}
