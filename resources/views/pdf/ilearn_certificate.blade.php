<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Cinzel&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cinzel:700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            
            font-family: 'Cinzel', serif;
            background: white;
            background-image: url('images/certificate-logo.png');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center top;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: -15 -10 -15 -10;
            /* padding: -300px; */
            /* text-align: center; */
        }

        .header {
            margin-bottom: 20px;
            
            text-align: right;
            font-size: 12px;
        }

        .header img {
            max-width: 100px;
            margin-left: auto;
            margin-right: 0;
            text-align: left;
            margin-top: 5px;
        }

        .certificate-no {
            text-align: left;
            margin-bottom: 20px;
            font-size: 14px;
            text-align: left;
        }

        .date {
            margin-bottom: 30px;
            font-size: 14px;
            text-align: left;
        }

        .certificate-title {
            font-family: 'Cinzel', serif;
            font-size: 20pt;
            /* text-transform: uppercase; */
            margin-bottom: 10px;
            font-weight: bold;
            text-align: left;
        }

        .certificate-title-two {
            font-family: 'Cinzel', serif;
            font-size: 18pt;
            /* text-transform: uppercase; */
            margin-bottom: 20px;
            font-weight: bold;
            text-align: left;
        }

        .recipient {
            font-size: 26pt;
            text-transform: uppercase;
            margin-bottom: 20px;
            font-weight: bold;
            text-align: left;
        }

        .course-details {
            margin-bottom: 20px;
            font-size: 16px;
            text-align: left;
        }

        .course-details-two {
            margin-bottom: 20px;
            font-size: 16px;
            text-align: left;
            font-weight: bold;
            font-style: italic;
        }

        .modules-list {
            margin-bottom: 20px;
            font-size: 16px;
            text-align: left;
        }

        .modules-list ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .proficiency {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: left;
        }

        .developed-by {
            font-size: 16px;
            margin-bottom: 50px;
            text-align: left;
        }

        .signature-section {
            text-align: left;
            padding-top: 30px;
        }

        .signature {
            margin-bottom: 5px;
            text-align: left;
        }

        .signature img {
            width: 10%;
        }

        .signatory-name {
            font-size: 14px;
            font-weight: bold;
        }

        .signatory-title {
            font-size: 14px;
        }

        .qr-code {
            position: absolute;
            left: 600px;
            bottom: 100px;
        }

        .qr-code img {
            width: 90px;
        }

        .logo-container {
            text-align: left;
        }

        .right-column {
            padding-right: -300px;
            position: absolute;
            bottom: -100px;              /* Stretches to the bottom */
            top: -100px;                 /* Stretches to the top */
            right: 0;               /* Aligns to the right */
            width: 13%;             /* Set width as per your requirement */
            background-color: #203484; /* Set the background color */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-no">
           <br/> <strong>Certificate NO:</strong> IL-CPD-{{$course_id}}-{{ str_pad($admission_id, 3, '0', STR_PAD_LEFT) }}
        </div>
        <div class="logo-container">
            <img src="images/ilearn-logo.png" width="20%" height="auto" alt="iLearn Logo" align="left" style="border-radius: 5%;">
        </div>

        <div class="date">
        <br/><br/><br/><br/><br/>Date issued: {{ \Carbon\Carbon::parse($admission_date)->format('F j, Y') }}.
        </div>
        <!-- <div class="certificate-title">
        Certificate of Professional Development
</div> -->

<div class="certificate-title">
        Certificate of Professional Development
</div>
<br/>
<!-- <div class="course-details-two">
            This is to certify that:
        </div>
<br/> -->
<div class="recipient">
    @if(!empty($name_on_certificate))
        {{$name_on_certificate}}
    @else
        {{$firstname}} {{$othernames}} {{$surname}}
    @endif
</div>
<br/>
 <div class="certificate-title">
        {{$certification_name}}
</div>



        <div class="course-details">
AWARDED FOR SUCCESSFUL COMPLETION OF A CPD CERTIFIED PROGRAM.        </div>

        <div class="modules-list">
            <strong>Core Skills Covered:</strong>
            @if(!empty($modules) && is_array($modules))
                <ul>
                    @foreach($modules as $module)
                        <li>{{ $module }}</li>
                    @endforeach
                </ul>
            @else
                <p>No modules available.</p>
            @endif
        </div>

        @php
    $words = explode(' ', $certification_name);
    $firstTwoWords = implode(' ', array_slice($words, 0, 2));
    $remainingWords = implode(' ', array_slice($words, 2));
@endphp


        <!-- <div class="proficiency"> -->
            <!-- Proficiency Certificate in<br> -->
            <!-- {{$certification_name}} -->
            <!-- {{ $firstTwoWords }}<br>{{ $remainingWords }} -->
        <!-- </div> -->
        <!-- <div class="developed-by">
            This course has been developed and delivered by iLearn Africa
        </div> -->

        <div class="right-column">
          <span style="margin-right:10px"><img src="images/logo1.png" width="140px" height="auto" style="padding-right: -300px; margin-top:600px; background:white;"/></span> 
    </div> 

        <div class="signature-section">
            <div class="signature">
                <img src="images/cd-sign.png" alt="Signature"><br/>
            <u>                                            </u>
            </div>
            <!-- <div class="signatory-name">
                Nwude Ifeoma Grace
            </div>  -->
            <div class="signatory-title">
            Training Director, iLearn Africa
            </div>
        </div>
        <div class="qr-code">
            <img src="{{ $qr_code }}" alt="QR Code">
        </div>
    </div>
</body>
</html>