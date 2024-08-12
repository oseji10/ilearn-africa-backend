<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{

    public function generateReceipt(Request $request)
    {
        $data = [
            'invoiceNumber' => "100",
            'clientName' => "Victor",
            'amount' => 1000,
            'date' => "Today",
        ];
    
        // Load the view file and pass in the data
        $pdf = Pdf::loadView('pdf.receipt', $data);
    
        // Return the generated PDF
        return $pdf->download("invoice-{$request->invoiceNumber}.pdf");
    }
    
}
