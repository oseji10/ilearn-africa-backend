<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $auto_password;

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
        return $this->view('emails.welcome-email')
                    ->subject('Welcome to iLearn Africa')
                    ->with([
                        'email' => $this->user->email,
                        'client_id' => $this->user->client_id,
                        'phone_number' => $this->user->phone_number,
                        'password' => $this->auto_password,
                        'action_url' => "https://app.ilearnafricaedu.com/auth/signin/",
                        'login_url' => "https://app.ilearnafricaedu.com/auth/signin/",
                        'firstname' => $this->user->client->firstname, // Accessing the client's firstname
                        'support_email' => "support@ilearnafricaedu.ng",
                    ]);
    }
}
