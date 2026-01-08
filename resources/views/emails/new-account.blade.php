<!DOCTYPE html>
<html>
<head>
    <title>Welcome to TFMS</title>
</head>
<body>
    <h1>Welcome to TFMS</h1>
    <p>Dear {{ $user->name }},</p>
    
    <p>An account has been created for you in the Task Force Monitoring System (TFMS).</p>
    
    <p><strong>Login Details:</strong></p>
    <ul>
        <li><strong>Email:</strong> {{ $user->email }}</li>
        <li><strong>Temporary Password:</strong> {{ $password }}</li>
    </ul>
    
    <p>Please log in at <a href="{{ route('login') }}">{{ route('login') }}</a>.</p>
    <p>You will be required to change your password upon your first login.</p>
    
    <p>Best regards,<br>TFMS Admin Team</p>
</body>
</html>
