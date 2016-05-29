<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<p>Dear Partner,</p><br/>
<p>You requested help with your Sportofitt account password. Please click the link below to set your new password.
</p><br/>
@if($user->role_id == 3)
<p><a href="{{ url('/#/reset-password', array($token)) }}">{{ url('/#/reset-password', array($token)) }}</a></p><br/>
@else
    <p><a href="{{ url('/select/#/reset-password', array($token)) }}">{{ url('/select/#/reset-password', array($token)) }}</a></p><br/>
@endif
<p>If clicking the link above doesn't work, please copy and paste the URL in a new browser window instead.
    Please ignore this email if it was not you who requested help with your password. Your current password will remain unchanged.
</p><br/>
<p>Regards,<br>
    Sportofitt Support
</p>
<p>select@sportofitt.com | Call +91-9457912886 | <a href="http://www.sportofitt.com" target="_blank">www.sportofitt.com</a></p>
</body>
</html>
