<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $auto_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user_data)
    {
        // $this->data = $data;
        $this->user_data = $user_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate the PDF
        $pdf = Pdf::loadView('pdf.receipt', $this->user_data); // Use Pdf (imported correctly at the top)


        return $this->view('emails.email-receipt')
                    ->subject('iLearn Africa - Payment Receipt')
                    ->attachData($pdf->output(), 'receipt.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        
                        'firstname' => $this->user_data['firstname'], // Accessing the client's firstname
                        'client_id' => $this->user_data['client_id'],
                        'amount' => $this->user_data['amount'],
                        
                        
                        'surname' => $this->user_data['surname'],
                        'othernames' => $this->user_data['othernames'],
                        'phone_number' => $this->user_data['phone_number'],
                        'email' => $this->user_data['email'],
                        'payment_method' => $this->user_data['payment_method'],
                        'transaction_reference' => $this->user_data['transaction_reference'],
                        'course_name' => $this->user_data['course_name'],
                        'course_id' => $this->user_data['course_id'],
                        'transaction_date' => $this->user_data['transaction_date'],
                        
                        'support_email' => "support@ilearnafricaedu.com",
                    ]);
    }



}
