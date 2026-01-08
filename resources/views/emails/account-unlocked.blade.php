<!DOCTYPE html>
<html>
<head>
    <title>Account Unlocked</title>
</head>
<body>
    <h1>Your Account Has Been Unlocked</h1>
    <p>Hello {{ $user->name }},</p>
    <p>Your account has been unlocked by an administrator. You can now log in to the system.</p>
    <p><a href="{{ route('login') }}">Login to TFMS</a></p>
    <p>If you did not request this, please contact the administrator immediately.</p>
</body>
</html>