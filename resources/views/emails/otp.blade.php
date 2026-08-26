<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 480px; margin: 40px auto; background: #fff; border-radius: 8px; padding: 32px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .logo { font-size: 24px; font-weight: bold; color: #1a1a1a; margin-bottom: 24px; }
        .otp-box { background: #f0f4ff; border-radius: 8px; padding: 24px; text-align: center; margin: 24px 0; }
        .otp { font-size: 40px; font-weight: bold; letter-spacing: 8px; color: #3b5bdb; }
        .footer { font-size: 12px; color: #999; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">Nilbox</div>
        <p>Hi there,</p>
        <p>Your <strong>{{ $messageType }}</strong> OTP is:</p>
        <div class="otp-box">
            <div class="otp">{{ $otp }}</div>
        </div>
        <p>This OTP is valid for <strong>5 minutes</strong>. Do not share it with anyone.</p>
        <div class="footer">
            If you did not request this, please ignore this email.<br>
            &copy; {{ date('Y') }} Nilbox. All rights reserved.
        </div>
    </div>
</body>
</html>