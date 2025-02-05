<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Letter</title>
    <link href="https://fonts.googleapis.com/css2?family=Good+Vibes&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">


    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            line-height: 1.6;
            background-color: #f4f4f4;
            margin-bottom: -100px; /* Adjusted to ensure footer is visible */
        }

        .container {
            width: 100%;
            background-color: #ffffff;
            padding-bottom: 150px; /* Make space for the footer */
        }

        .header {
            text-align: right;
            margin-bottom: 30px;
        }

        .header img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .header p {
            margin: 0;
            font-size: 24px;
            font-style: italic;
            font-family: 'Times New Roman', serif;
            color: #333;
        }

        .content {
            margin-bottom: 30px;
        }

        .content p {
            font-size: 16px;
            color: #555;
        }

        .signature {
            margin-top: 40px;
            text-align: left;
        }

        .signature p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }

        .signature span {
            display: block;
            margin-top: 5px;
        }

        .footer {
            position: fixed;
            bottom: -40;
            left: -50;
            right: -300;
            width: 150%;
            height: 100px;
            background-color: #d8e4f4; /* Change this to your desired footer color */
            color: #000;
            text-align: center;
            line-height: 50px; /* Vertically center the text */
            margin: 0;
            font-size:12px;
        }
        .footer p{
            z-index: -1;
        }

        .right-edge-image {
            position: fixed;
            top: -40;
            right: -50;
            /* top: 0; */
            height: 100%;
            width: auto; /* Maintain aspect ratio */
            z-index: -1; /* Ensure it stays behind other content */
            background-repeat: no-repeat; /* Prevent repeating */
        }
        .good-vibration {
            font-family: 'Good Vibes', cursive;
        }
    </style>
</head>
<body>
    <img src="images/edge.png" alt="Right Edge Design" class="right-edge-image">
    <div class="container">
        <div class="header">
            <img src="images/ilearn-logo.png" alt="Logo" align="left"><br/>
            <img src="images/redefining.png" style="margin-right:30;" />
            <!-- <p style="margin-right:30; font-size: 34px;" class="good-vibration">Redefining Learning</p> -->
        </div>
        <hr style="margin-right:30;"/>
        <p align="right" style="margin-right:30; margin-top:-10">{{ \Carbon\Carbon::parse($admission_date)->format('F jS, Y') }}.</p>
        <p align="center" style="color:blue;">ADMISSION LETTER</p>
        <div class="content">
            <b>Dear {{$firstname}} {{$othernames}} {{$surname}},</b><br/>
            <p>Congratulations!</p>
            
            <!-- <i>Following a comprehensive assessment of your application, encompassing your academic qualifications and pertinent credentials, we are delighted to convey that the board of ILEARN AFRICA has granted you admission into the {{$certification_name}} program of its affiliated institute; {{$center_name}}.</i>
            <i>This program will lead to the prestigious award of a {{$course_name}} from our esteemed partner institution and a proficiency certificate in SEND from ILEARN AFRICA.</p>
            <i>This course is slated to commence on the 10th of May, 2024, and will run for a duration of 5 weekends (Fridays only). You will be added to a dedicated WhatsApp group for registered participants soon where you would access the zoom link for training participation and other relevant material for this course.</i>
            <i>Kindly note that a penalty fee of ₦10,000 is applied for the re-sit of the professional examination in the event of failure to meet the minimum score of 250 in your first attempt and that the re-sit option is only once.</i>
            <i>We extend our warmest wishes for a successful and enriching academic journey ahead. Should you have any queries or require further assistance, please do not hesitate to contact us.</i> --> 
        
            <p>Following a comprehensive assessment of your application, encompassing your academic qualifications and pertinent credentials, we are delighted to convey that the board of ILEARN AFRICA has granted you admission into her <b style="text-transform:uppercase;">{{$course_name}} program.</b></p>

            <p>This program will lead to the prestigious award of <b style="text-transform:uppercase;">{{$certification_name}}</b>. This course is slated to commence on the <b>{{ \Carbon\Carbon::parse($start_date)->format('jS F Y') }}.</b></p>
            <!-- <p>You will be added to a dedicated WhatsApp group for registered participants soon where you would access the zoom link for training participation and other relevant material for this course. </p>
<p>Kindly note that a minimum mandatory participation attendance of 75% is required for certification. </p> -->
<p>We extend our warmest wishes for a successful and enriching academic journey ahead. Should you have any queries or require further assistance, please do not hesitate to contact us.</p>

        </div>

        <div class="signature">
            <i>Yours Sincerely,</i><br/>
            <u style="margin:0;"><img src="images/cd-sign.png" /><br/>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u><br/>
            <i>
            <!-- Nwude Ifeoma Grace -->
            <br> 
            Programs Director
             <br>
            iLearn Africa 
            </i>
        </div>

      

        <!-- <div>
                    <p>Yours Sincerely,</p><br/>
                    <u style="margin:0;"><img src="images/cd-sign.png" /><br/>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
                    <p style="font-size:16pt; font-weight:bold; margin:0;">Nwude Ifeoma Grace</p>
                    <p style="margin:0;">Programme Director/iLearn Africa</p>
                </div> -->

    </div>
    <footer class="footer">
        <!-- <p>Redefining Learning</p> -->
        <table  width="80%" style="top:15px; padding-left: -20px">
            <tr>
                <td align="right" width="20%">+2349160913155</td>
                <td align="center">admin@ilearnafricaedu.com</td>
                <td width="40%">Suite 308 Nawa Complex Kado, Abuja,
                Nigeria</td>
                <!-- <td>kjdjkzjjjjjjjjj</td> -->
    </tr>
    </table>
    </footer>
</body>
</html>
