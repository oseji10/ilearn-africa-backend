<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class EmailAdmission extends Mailable
{
    use Queueable, SerializesModels;

    public $admission_data;
    // public $auto_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($admission_data)
    {
        // $this->data = $data;
        $this->admission_data = $admission_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
{
    // Retrieve center name from admission data
    $centerName = $this->admission_data['center_name'];

    // Determine which PDF view to load based on the center name
    if (strpos($centerName, 'iLearn Africa') !== false) {
        $pdf = Pdf::loadView('pdf.ilearn_admission_letter', $this->admission_data);
    } else {
        $pdf = Pdf::loadView('pdf.partner_admission_letter', $this->admission_data);
    }

    // Attach the generated PDF to the email
    return $this->view('emails.email-admission')
                ->subject('iLearn Africa - Admission Letter')
                ->attachData($pdf->output(), 'admission.pdf', [
                    'mime' => 'application/pdf',
                ])
                ->with([
                    'firstname' => $this->admission_data['firstname'],
                    'client_id' => $this->admission_data['client_id'],
                    'amount' => $this->admission_data['amount'],
                    'surname' => $this->admission_data['surname'],
                    'othernames' => $this->admission_data['othernames'],
                    'phone_number' => $this->admission_data['phone_number'],
                    'email' => $this->admission_data['email'],
                    'payment_method' => $this->admission_data['payment_method'],
                    'transaction_reference' => $this->admission_data['transaction_reference'],
                    'course_name' => $this->admission_data['course_name'],
                    'course_id' => $this->admission_data['course_id'],
                    'support_email' => "support@ilearnafricaedu.com",
                    'admission_number' => $this->admission_data['admission_number'],
                    'admission_date' => $this->admission_data['admission_date'],
                ]);
}




}
