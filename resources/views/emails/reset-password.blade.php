<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Your Password</h1>
    <p>You are receiving this email because we received a password reset request for your account.</p>
    <p>Click the link below to reset your password:</p>
    <a href="{{ route('password.reset', $token) }}">Reset Password</a>
    <p>If you did not request a password reset, no further action is required.</p>
    <p>This link will expire in 60 minutes.</p>
</body>
</html>