<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verified</title>
    <!-- <link rel="stylesheet" href="styles.css"> -->
     <style>
        body {
    margin: 0;
    padding: 0;
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.verification-container {
    text-align: center;
    padding: 30px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.verification-box {
    max-width: 400px;
    margin: 0 auto;
}

.checkmark {
    font-size: 50px;
    color: #4CAF50;
    margin-bottom: 20px;
    animation: pop 0.3s ease-out;
}

h1 {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;
}

p {
    font-size: 18px;
    color: #777;
}

@keyframes pop {
    0% {
        transform: scale(0.7);
        opacity: 0.5;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-box">
            <div class="checkmark">&#10004;</div>
            <h1>Verified!</h1>
            <p>This payment is verified.</p>
        </div>
    </div>
</body>
</html>
