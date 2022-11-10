<?php

return [
    "email" => [
        "email" => "Введите адрес электронной почты в правильном формате!",
        "required" => "Требуется заполнение!",
        "unique" => "Пользователь с такой почтой уже существует!"
    ],
    "emailError" => "Elektron pochta yoki parol notogri. Qayta urinib koring",
    "loggingin" => "Авторизоваться",
    "name" => [
        "required" => "Требуется заполнение!",
        "unique" => "Пользователь с таким именем уже существует!",
        "int"=>"Неверный формат",
        "string"=>"Текстовое поле должно быть строкой."
    ],
    "password" => [
        "confirmed" => "пароль не тот",
        "min" => "Пароли должны содержать не менее 8 символов",
        "required" => "Требуется заполнение!"
    ],
    "phone_number" => [
        "int" => "Неверный формат номера телефона!",
        "min" => "Неверный формат номера телефона!",
        "regex" => "Неверный формат поля",
        "required" => "Требуется заполнение!",
        "unique" => "Этот номер есть в системе!",
        "numeric"=> "Поле должно быть числом",
        "exists"=> "Значение, выбранное для номера телефона, неверно",
    ],
    "signin_below" => "Доступ к панели управления",
    "welcome" => "Панель управления, которой не хватало в Laravel"
];
