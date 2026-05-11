<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #1e293b 0%, #7c3aed 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .credentials-box {
            background: white;
            border: 2px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-row {
            margin: 10px 0;
            padding: 10px;
            background: #eff6ff;
            border-radius: 5px;
        }
        .credential-label {
            font-weight: bold;
            color: #1e40af;
        }
        .credential-value {
            font-family: 'Courier New', monospace;
            color: #1e293b;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            background: #3b82f6;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin: 20px 0;
        }
        .warning {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #64748b;
            font-size: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name') }}</h1>
        <p>Your account has been created</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $name }}</strong>,</p>

        <p>Your account has been successfully created. Below are your login credentials to access the system:</p>

        <div class="credentials-box">
            <h3 style="margin-top: 0; color: #1e40af;">Your Login Credentials</h3>
            
            <div class="credential-row">
                <span class="credential-label">Email:</span><br>
                <span class="credential-value">{{ $email }}</span>
            </div>

            <div class="credential-row">
                <span class="credential-label">Password:</span><br>
                <span class="credential-value">{{ $password }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $login_url }}" class="button">Login to Your Account</a>
        </div>

        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <ul style="margin: 10px 0;">
                <li>Please change your password after your first login</li>
                <li>Do not share your login credentials with anyone</li>
                <li>Keep this email secure or delete it after changing your password</li>
            </ul>
        </div>

        <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>

        <p>Best regards,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>

    <div class="footer">
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>