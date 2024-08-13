<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    
    <style>
        body {
            position: relative;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            font-size: 5em;
            color: #000;
            user-select: none;
            z-index: -1;
        }
        .watermark img {
            width: 150%; /* Zoom in the image */
        height: auto;
        transform: rotate(-45deg);
        filter: brightness(0.5);
        }
    </style>
</head>
<body>
<div class="watermark">
    <img src="{{ public_path('images/ilearn-logo.png') }}" alt="Watermark" style="width: 100%; height: auto;">
</div>
    <table width="100%" style="text-align:center";>
        <tr>
            <td style="width:30%"><img src="images/ilearn-logo.png" width="100%"/></td>
            <td>
                <h2>iLEARN AFRICA</h2>
                Address: Suite 308 Nawa Complex Kado Abuja, Nigeria.<br/>
                Email: info@ilearnafricaedu.com website: www.ilearnafricadu.com<br/>
                Phone No: +234 9160 913 155<br/>
            </td>
</tr>
</table>

<h2>CLIENT RECEIPT<h2>
<table align="center" border="1px" style="border-collapse:collapse;" width="100%">
    <tr>
        <td colspan="2"><h4>PERSONAL INFORMATION</b></h4></td>
        
    </tr>
    <tr>
        <td><h4>Client ID: <b>{{ $client_id }}</b></h4></td>
        <td align="right"><h4>Receipt # <b style="font-size:20px;">{{ $client_id ?? ''}}</b></h4></td>
    </tr>
    <tr>
    <td colspan="2"><h4>Client Name: <b style="font-size:20px;">{{ $surname ?? ''}}, {{ $firstname ?? ''}} {{ $othernames ?? ''}}</b></h4></td>
    </tr>  

    <tr>
        <td><h4>Email: <b style="font-size:20px;">{{ $email ?? '' }}</b></h4></td>
        <td><h4>Phone Number: <b style="font-size:20px;">{{ $phone_number ?? ''}}</b></h4></td>
        <!-- <td><h4>Receipt # <b>{{ $client_id }}</b></h4></td> -->
    </tr>
    </table>

    <table align="left">
    <tr>
       <td>
           </td> 
        </tr>  
    </table>

<br/>

    <table align="center" border="1px" style="border-collapse:collapse;" width="100%">
    <tr>
        <td colspan="2"><h4>COURSE REGISTRATION DETAILS</b></h4></td>
        
    </tr>
    <tr>
        <td width="50%"><h4>Course ID: <b style="font-size:20px;">{{ $course_id }}</b></h4></td>
        <td align="left"><h4>Course Name: <b style="font-size:20px;">{{ $course_name ?? ''}}</b></h4></td>
    </tr>
   
    </table>
<br/>
    <table align="center" border="1px" style="border-collapse:collapse;" width="100%">
    <tr >
        <td colspan="2"><h4>PAYMENT DETAILS</b></h4></td>
        
    </tr>
    <tr>
        <td align="left" width="50%"><h4>Payment Date: <b style="font-size:20px;">{{ $created_at ?? ''}}</b></h4></td>
        <td><h4>Payment Method: <b style="font-size:20px;">{{ $payment_method }}</b></h4></td>
    </tr>
    
    <tr>
        <td align="left"><h4>Transaction Reference: <b style="font-size:20px;">{{ $transaction_reference ?? ''}}</b></h4></td>
        <td><h4>Amount: <b style="font-size:20px;">NGN{{number_format(($amount),2) }}</b></h4></td>
    </tr>
    <tr>
    <td colspan="2" ><h4>Amount in words<br/><b style="font-size:20px;">{{ucwords(strtolower($amount_in_words))}} naira, only.</b></h4></td>
    </tr>
    </table>

    <table align="left">
    <tr>
       <td>
           </td> 
        </tr>  
    </table>
    <table>
        <tr>
            <td>Verify this payment by scanning this code:</td>
        </tr>
        <tr>
        <td><img src="{{ $qr_code }}" alt="QR Code">
        </td>

        </tr>
    </table>
    
</body>
</html>
