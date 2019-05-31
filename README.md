# securityLib
**securityLib** - это класс для создания сертификатов и проверки их на действительность. Сертификаты нужны для надёжного распространения информации

## Пример работы

```php
<?php

$securityLib = new securityLib ('My super key'); // "My super key" - ключ шифрования. Не сообщайте его никому!

# "storage" - главное хранилище сертификата. Используйте его как душе угодно
$securityLib->storage = array
(
    'hello' => 'world'
);

$securityLib->saveCertificate ('certificate.crt'); // Сохранение сертификата в файл "certificate.crt"
```

Сертификат так же обладает свойством **dieAfter**. Это свойство определяет время в формате **UNIX**, после которого сертификат перестанет быть действительным

```php
<?php

$securityLib = new securityLib ('My super key');
$securityLib->storage = array
(
    'hello' => 'world'
);

$securityLib->dieAfter = time () + 10 * 60; // time () возвращает текущее UNIX время в секундах. 10 * 60 - это 10 раз по 60 секунд, т.е. 10 минут. Конструкция означает, что сертификат перестанет работать ровно через 10 минут

$securityLib->saveCertificate ('certificate.crt');
```

Открыть сертификат можно как через второй аргумент конструктора, так и через метод **loadCertificate**, при этомвы можете передать как путь до файла сертификата, так и сам сертификат

```php
<?php

$securityLib = new securityLib ('My super key'); // Здесь можно так же дописать "certificate.crt" вторым аргументом и не вызывать метод loadCertificate

echo (int) $securityLib->loadCertificate ('certificate.crt'); // Подгружаем сертификат. Метод возвращает bool статус сертификата (true если он действителен), так что мы выводим ответ в виде цифры

echo (int) $securityLib->status; // Статус сертификата (то же значение, что и возвращает метод выше)
```

Удачной работы!

Автор: [Подвирный Никита](https://vk.com/technomindlp). Специально для [Enfesto Studio Group](http://vk.com/hphp_convertation)