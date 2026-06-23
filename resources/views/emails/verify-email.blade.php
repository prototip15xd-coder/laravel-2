<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Подтверждение email</title>
</head>
<body>
<div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <h1 style="color: #333;">Laravel Shop</h1>

    <p>Здравствуйте, <strong>{{ $user->name }}</strong>!</p>

    <p>Вы зарегистрировались на нашем сайте.</p>

    <p>Нажмите на кнопку, чтобы подтвердить адрес электронной почты:</p>

    <a href="{{ $verificationUrl }}"
       style="display: inline-block; padding: 12px 24px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">
        Подтвердить email
    </a>

    <p style="margin-top: 20px; font-size: 14px; color: #888;">
        Если вы не регистрировались, просто проигнорируйте это письмо.
    </p>

    <hr>

    <p style="font-size: 12px; color: #aaa;">
        С уважением, команда Laravel Shop
    </p>
</div>
</body>
</html>
