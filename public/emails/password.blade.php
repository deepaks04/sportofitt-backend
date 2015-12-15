<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>
<h2>Password Reset</h2>

<div>
    To reset your password,<a href="{{ url('/#/select/reset-password', array($token)) }}"> Click on this Link to reset. </a><br/>
    This link will expire in {{ Config::get('auth.reminder.expire', 60) }} minutes.
</div>
</body>
</html>
