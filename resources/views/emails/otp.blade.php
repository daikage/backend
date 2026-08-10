<!DOCTYPE html>
<html>
<head>
    <title>Password Reset OTP</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Password Reset Request</h2>
    <p>We received a request to reset your password. Use the following OTP to proceed:</p>
    
    <div style="margin: 20px 0; font-size: 24px; font-weight: bold; padding: 15px; background: #f4f4f4; text-align: center; border-radius: 8px;">
        {{ $otp }}
    </div>
    
    <p>This code will expire in 15 minutes.</p>
    <p>If you did not request a password reset, please ignore this email.</p>
    
    <br>
    <p>Regards,<br>Pairride Team</p>
</body>
</html>
