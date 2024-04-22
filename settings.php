<?php

require __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

set_error_handler('my_error_handler');
register_shutdown_function('my_shutdown_handler');

// Параметры подключения
$dbConfig = require CONFIG . '/database.php';
$telegram = new Telegram(TELEGRAM_BOT_TOKEN, TELEGRAM_BOT_NAME);

try 
{
    // Создание подключения
    $conn = mysqli_connect
    (
        $dbConfig['host'],
        $dbConfig['username'], 
        $dbConfig['password'],
        $dbConfig['dbname']
    );
} 
catch(Exception $e) 
{
    throw $e;
}

if (tryGetTable($conn, $sources, 'source'))
    debug($sources['rows']);

$temp = [];

var_dump(array_search('page.zub-ekb.ru', $sources['rows']));

// foreach($sources as $source) 
// {
//     if (array_search($source['domen'], $temp) !== false) 
//     {
//         if ()

//     }
// }