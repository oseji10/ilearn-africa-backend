<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Cinzel' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Cinzel+Bold' rel='stylesheet'>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding-bottom: -100;
        }

        body {
            /* background-image: url('images/certificate-logo.png'); Replace with your image path */
            background-size: cover; /* Ensures the image covers the whole page */
            background-position: center; /* Centers the image */
            background-repeat: no-repeat; /* Prevents the image from repeating */
            background-attachment: fixed; /* Keeps the image fixed in place when scrolling */
            line-height: 1.6;
            display: flex;
            align-items: stretch;
        }

        table {
            height: 100%;
            width: 100%;
            border-collapse: collapse;
            background-color: transparent; /* Ensure the table background is transparent */
        }

        td {
            margin: 0px;
            padding: 10px;
            vertical-align: top;
            background-color: transparent; /* Ensure the cell background is transparent */
        }

        .left-column {
            width: 82%;
            background-image: url('images/certificate-logo.png');
            
            /* background-color: rgba(240, 240, 240, 0.8); Semi-transparent background color */
        }

        .first-column {
            width: 2%;
        }
        .right-column {
            width: 11%;
            background-color: #203484; /* Semi-transparent background color */
        }

        .third-column {
            width: 5%;
            /* background-color: rgba(224, 224, 224, 0.8); Semi-transparent background color */
        }

        .header {
            text-align: right;
            margin-bottom: 30px;
        }

        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        
    </style>
</head>
<body>
    <table>
        <tr>
        <td class="first-column">
         </td>

            <td class="left-column">
                <div class="header">
                    <p style="font" align="left"><b>Certificate NO:</b> iLA/{{ str_pad($id, 3, '0', STR_PAD_LEFT) }}/{{ \Carbon\Carbon::parse($admission_date)->format('Y') }}</p>
                    <img src="images/ilearn-logo.png" alt="Logo" align="left" style="border-radius: 5%;"><br/><br/><br/>
                    
                    <p align="left">On {{ \Carbon\Carbon::parse($admission_date)->format('F j, Y') }}.</p>
                    <p align="left" style="font-size: 35pt; margin:0;  font-family: 'Cinzel Bold'; text-transform: uppercase;">
                        {{$firstname}} {{$othernames}} {{$surname}}
                    </p>
                    <p align="left">Has Successfully Completed a Course in<br/>
                    {{$certification_name}} and Has Been Awarded this</p>
                    <p align="left" style="font-size:30px; margin:0; font-weight: bold;">
                        Proficiency certificate in<br/>
                        {{$certification_name}}
                    </p>
                    <p align="left">This course has been developed and delivered by iLearn Africa</p>
                </div>
           
   

                <div>
                    <p>Yours Sincerely,</p>
                    <u style="margin:0;"><img src="images/cd-sign.png" /><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                    <p style="font-size:16pt; font-weight:bold; margin:0;">Nwude Ifeoma Grace</p>
                    <p style="margin:0;">Programme Director/iLearn Africa</p>
                </div>
            </td>
           <td class="right-column">
    <img src="images/logo1.png" width="150px" style="margin-left: -10px; margin-right: -15px; margin-top:500px; background:white;"/>
</td>


            <td class="third-column">
            <p align="right"> <img width="70px" height="auto" align="right" style="display: table-cell; vertical-align: bottom; text-align: center;" src="{{ $qr_code }}" alt="QR Code"></p>
            </td>
        </tr>
    </table>
</body>
</html>
