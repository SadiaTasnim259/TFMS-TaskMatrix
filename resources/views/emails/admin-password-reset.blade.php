<!DOCTYPE html>
<html>

<head>
    <title>Password Reset</title>
</head>

<body>
    <h2>Hello {{ $user->name }},</h2>
    <p>Your password reset request has been processed by an administrator.</p>
    <p>Please click the button below to reset your password:</p>
    <p>
        <a href="{{ route('password.reset', $token) }}"
            style="display: inline-block; padding: 10px 20px; background-color: #721c24; color: #ffffff; text-decoration: none; border-radius: 5px;">Reset
            Password</a>
    </p>
    <p>This link will expire in 60 minutes.</p>
    <p>If the button doesn't work, copy and paste this link into your browser:</p>
    <p>{{ route('password.reset', $token) }}</p>
    <p>If you did not request this change, please ignore this email.</p>
</body>

</html>