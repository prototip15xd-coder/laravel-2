<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добро пожаловать в Laravel Shop</title>
</head>
<body>
<div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
    <h1 style="color: #333;">Laravel Shop</h1>

    <p>Здравствуйте, <strong>{{ $user->name }}</strong>!</p>

    <p>Мы рады приветствовать вас в нашем магазине. Ваш аккаунт успешно подтверждён, и теперь вы можете пользоваться всеми возможностями сайта.</p>

    <p>В нашем каталоге вы найдёте множество товаров на любой вкус. Желаем вам приятных покупок!</p>

    <a href="{{ route('products.index') }}"
       style="display: inline-block; padding: 12px 24px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">
        Перейти в каталог
    </a>

    <p style="margin-top: 20px; font-size: 14px; color: #888;">
        Если у вас возникнут вопросы, напишите нам на support@laravel-shop.ru
    </p>

    <hr>

    <p style="font-size: 12px; color: #aaa;">
        С уважением, команда Laravel Shop
    </p>
</div>
</body>
</html>
