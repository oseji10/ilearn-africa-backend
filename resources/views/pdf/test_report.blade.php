<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iLearn Africa Test Report Form</title>
    <style>
     
        .header {
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }
        .small-header {
            text-align: left;
            font-weight: bold;
            font-size: 18px;
            /* border: 2px solid black; */
            /* padding: 10px; */
            margin-bottom: 1px;
        }
        .sub-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 10px;
        }

        .sub-header2 {
    text-align: center;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid black;
    width: 30%;
    padding: 10px;
    margin-bottom: 10px;
    margin-left: auto;  /* Pushes the box to the right */
    display: block;  /* Ensures it's a block-level element */
}

.blocks {
    text-align: left;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid black;
    width: auto;
    padding: 10px;
    margin-bottom: 10px;
    margin-left: auto;  /* Pushes the box to the right */
    /* display: block;  */
    display: inline;
    margin: 0;  /* Ensures it's a block-level element */
}

.blocks2 {
    text-align: left;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid black;
    width: 70%;
    padding: 10px;
    margin-bottom: 10px;
    margin-left: auto;  /* Pushes the box to the right */
    display: block; 
    /* display: inline; */
    margin: 0; 
    background-color:rgb(215, 212, 212);
}

.blocks3 {
    text-align: left;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid black;
    width: 35%;
    padding: 10px;
    margin-bottom: 10px;
    margin-left: auto;  /* Pushes the box to the right */
    display: block; 
    /* display: inline; */
    margin: 0; 
    background-color:rgb(215, 212, 212);
}

        .box {
            border: 2px solid black;
            padding: 10px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: column;
        }
        .row {
            display: flex;
            justify-content: space-between;
            border: 1px solid black;
            padding: 5px;
            margin-bottom: 5px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table, .table th, .table td {
            border: 2px solid black;
            text-align: center;
            padding: 8px;
        }
        .footer {
            font-size: 12px;
            text-align: center;
            margin-top: 20px;
            border: 2px solid black;
            padding: 10px;
        }

        body {
    font-family: Georgia, Times, "Times New Roman", serif;
    /* font-family: Arial, sans-serif; */
    padding: 2px;
    max-width: 800px;
    margin: auto;
    /* border: 2px solid black; */
}

    </style>
</head>
<body>
    <!-- <div class="header">Test Report Form</div> -->
     <!-- <img src="/images/ilearn-logo.png" alt="iLearn Africa Logo" align="left" width="200px"><br/><br/><br/><br/><br/><br/> -->
     <img src="{{ public_path('images/ilearn-logo.png') }}" alt="iLearn Africa Logo" width="150px">

     <div>
    <h3 style="float: left; margin: 0;">Examination</h3>
    <span class="sub-header2" style="float: right; text-transform:uppercase;">{{$results->exam->course->course_name}}</span>
    <div style="clear: both;"></div> <!-- Ensures elements below don’t get affected -->
</div>


<div></div>
<table style="width: 100%; border-collapse: collapse;">
    <tr>
        <td style="text-align: left; width: 50%;">Date <span class="blocks">{{\Carbon\Carbon::parse($results->created_at ?? '')->format('d/M/Y')}}</span></td>
        <td style="text-align: right; width: 50%;">Admission Number <span class="blocks">{{$results->client->admissions->admission_number ?? ''}}</span></td>
    </tr>
</table><br/>
<hr/>


    <!-- <div class="box"> -->
    <div class="small-header">Candidate Details</div>
    <!-- <br/> -->
<table style="width: 100%; border-collapse: collapse;" >
    <tr>
        <td style=" padding-bottom: 25px; text-align: left; width: 15%;">First Name </td> 
        <td class="blocks2" style="text-transform: uppercase;">{{$results->client->firstname ?? ''}}</td>
        <td rowspan="4" style="width: 25%"><img src="{{ Storage::url('profile_images/' . $results->client->passport->image_url ?? '') }}" alt="Candidate Passport">

        <!-- {{ $results->client->passport->image_url }}     -->
    </td>
    </tr>
    
    <tr>
        <td style="padding-bottom: 25px; text-align: left; width: 15%;">Last Name </td> 
        <td  class="blocks2" style="text-transform: uppercase;">{{$results->client->surname ?? ''}}</td>
        <!-- <td></td> -->
    </tr>

    <tr>
        <td style="padding-bottom: 25px; text-align: left; width: 15%;">Other  Names </td> 
        <td  class="blocks2" style="text-transform: uppercase;">{{$results->client->othernames ?? ''}}</td>
        <!-- <td></td> -->
    </tr>

    
    <tr>
        <td style="padding-bottom: 25px; text-align: left; width: 15%;">Candidate ID </td> 
        <td colspan="2" class="blocks3" style="text-transform: uppercase;">{{$results->client->client_id ?? ''}}</td>
        <!-- <td></td> -->
    </tr>
</table>
<!-- <br/> -->
<hr/>



        
            <!-- <div class="row">
                <span><strong>Candidate Name:</strong> ESEOSA IDUSERI</span>
                <span><strong>Date of Birth:</strong> 25/07/1988</span>
            </div>
            <div class="row">
                <span><strong>Country of Origin:</strong> NIGERIA</span>
                <span><strong>Country of Nationality:</strong> NIGERIA</span>
            </div>
            <div class="row">
                <span><strong>First Language:</strong> ENGLISH</span>
            </div> -->
        <!-- </div> -->
        
        <div class="small-header">Test Results</div>
        <h3><strong>Course:</strong> {{$results->exam->course->course_name}}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Score</th>
                <th>Percentage</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                
                <td><strong>{{$results->total_score}}/{{$results->total_score2}}</strong></td>
                <td>{{ round(($results->total_score / $results->total_score2) * 100, 2) }}%
                </td>
                <td>{{ ($results->total_score2 != 0 && ($results->total_score / $results->total_score2) * 100 >= 50) ? 'PASS' : 'ADVISED TO RESIT' }}
                </td>
            </tr>
        </tbody>
    </table>
    
    <div class="footer">
    This document is a Result Slip and is issued for informational purposes only. It is not an official certificate and should not be used as a substitute for an officially issued certificate.
        <!-- <br>
        Kindly reach out if you have any questions or concerns. -->
        <br>
        <!-- <strong>Test Date:</strong> 24/07/2024 -->
    </div>
</body>
</html>