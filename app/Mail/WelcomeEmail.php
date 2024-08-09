<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $auto_password)
    {
        $this->user = $user;
        $this->auto_password = $auto_password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome')
                    ->subject('Welcome to Our Platform')
                    ->with([
                        'email' => $this->user->email,
                        'phone_number' => $this->user->phone_number,
                        'password' => $this->auto_password,
                    ]);
    }
}




