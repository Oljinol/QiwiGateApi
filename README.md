Класс для работы с API QiwiGate
=====================
###### qiwi-gate/qg-api

### ***Установка с помощью composer***

* Через терминал войти в дерикторию сайта и ввести команду:
```bash
composer require qiwi-gate/qg-api
```

* Если по каким то причинам у вас нет composer-а, то можно подключить так:
1. Создайте пустой файл в вашем проекте QiwiGate.php
2. Скоприруйте содиржимое файла из /src/QiwiGate.php и вставьте в свой файл QiwiGate.php
3. Подключите файл к вашему скрипту
```php
// file index.php
<?php
require_once './QiwiGate.php'; //путь до файла
```
4. Если настроили правильно, то все будет работать

### ***Применение***

* Инициализация:
```php
$purseAPIKey   = 'API токен кошелька';
$accountAPIKey = 'API токен аккаунта';

$qiwi = new QGA\QiwiGate($purseAPIKey, $accountAPIKey);
```

* Отправка запроса:
```php
$qiwi->sendRequest($method, $options = [], $assoc = true);

/*
 * Обязательный аргумент
 */ 
// Имя метода
$method  = 'account.info.wallet';

/*
 * Не обязательные аргументы
 */
 // Массив с параметрами 
$options = [                      
    'phone_key' => $purseAPIKey
];
// По умолчанию = true в qiwi->response записывется ассициативный массив
// Если отпарвить false в qiwi->response запишется объект
$assoc = false; 
```

* Результат запроса
```php
// Пример неуспешной обработки запроса qiwi->error === true
$qiwi->error = true || false;

// Ответ с сервера в виде ассициативного массива или объекта
$qiwi->response = [
    'status'   => 'success',
    'phone'    => '79000000000',
    'password' => 'xxx',
    'expire'   => 'dd.mm.yyyy',
    'pay'      => '1'
];
// ИЛИ
$qiwi->response->status    = 'success';
$qiwi->response->phone     = '79000000000';
$qiwi->response->password  = 'xxx';
$qiwi->response->expire    = 'dd.mm.yyyy';
$qiwi->response->pay       = '1';

// Ссылка которая была сформирована и по которой был отправлен запрос 
$qiwi->link = 'https://qiwigate.ru/api?key=API токен аккаунта&method=account.info.wallet?phone_key=API токен кошелька';
```

### ***Боевой пример***
```php
$purseAPIKey   = 'API токен кошелька';
$accountAPIKey = 'API токен аккаунта';

$qiwi = new QGA\QiwiGate($purseAPIKey, $accountAPIKey);

$options = [                      
    'start'    => '01.01.1970',
    'finish'   => '02.01.1970',
    'status'   => 'SUCCESS',
    'currency' => 'qiwi_RUB',
    'type'     => 'in'
];
$method  = 'qiwi.get.history';

$qiwi->sendRequest($method, $options, false);

if (!$qiwi->error){

    foreach ($qiwi->response->history as $payment) {
        if ($payment['comment'] === $myComment) {
            $db->writeToTheDatabase($payment);
        }
    }
    
} else {
    // Произошла ошибка
}
```