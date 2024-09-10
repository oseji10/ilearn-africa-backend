<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailCertificate extends Mailable
{
    use Queueable, SerializesModels;

    public $certificate_data;
    // public $auto_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($certificate_data)
    {
        // $this->data = $data;
        $this->certificate_data = $certificate_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Generate the PDF
        // $pdf = Pdf::loadView('pdf.certificate', $this->certificate_data); 
        
        $pdf = Pdf::loadView('pdf.certificate', $this->certificate_data)
          ->setPaper('a4', 'landscape');// Use Pdf (imported correctly at the top)


        return $this->view('emails.email-certificate')
                    ->subject('iLearn Africa - Certificate')
                    ->attachData($pdf->output(), 'certificate.pdf', [
                        'mime' => 'application/pdf',
                    ])
                    ->with([
                        
                        'firstname' => $this->certificate_data['firstname'], // Accessing the client's firstname
                        'client_id' => $this->certificate_data['client_id'],
                        'amount' => $this->certificate_data['amount'],
                        'surname' => $this->certificate_data['surname'],
                        'othernames' => $this->certificate_data['othernames'],
                        'phone_number' => $this->certificate_data['phone_number'],
                        'email' => $this->certificate_data['email'],
                        'payment_method' => $this->certificate_data['payment_method'],
                        'transaction_reference' => $this->certificate_data['transaction_reference'],
                        'course_name' => $this->certificate_data['course_name'],
                        'course_id' => $this->certificate_data['course_id'],
                        'support_email' => "support@ilearnafricaedu.com",
                        'admission_number' => $this->certificate_data['admission_number'],
                        'admission_date' => $this->certificate_data['admission_date'],
                        'name_on_certificate' => $this->certificate_data['name_on_certificate'],
                    ]);
    }



}
