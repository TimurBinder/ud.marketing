<?php

require __DIR__ . '/vendor/autoload.php';

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Request;

set_error_handler('my_error_handler');
register_shutdown_function('my_shutdown_handler');

$citiesBxIds = 
[
    72 => 51,
    66 => 53
];

$citiesNames =
[
    72 => 'Тюмень',
    66 => 'Екатеринбург'
];

if(empty($_POST)) 
{
    $requestJson = file_get_contents('php://input');

    if (isJson($requestJson))
        $_POST = json_decode($requestJson, true);
}

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
    echo $e;
    errorLog($e);
}

// Битрикс
try 
{
    if (!isset($_POST['bitrix_id'])) {
        $data = 
        [
            'fields' => [
                'TITLE' => $_POST['title'],
                'PHONE' => [
                    [
                        'VALUE' => $_POST['phone'],
                        'VALUE_TYPE' => 'WORK'
                    ]
                ],
                'UF_CRM_1698302617' => $_POST['domen'],                     // Домен
                'UF_CRM_1635751283979' => $citiesBxIds[$_POST['city']],     // Город
            ]
        ];

        $data['fields']['UTM_SOURCE'] = (isset($_POST['utm_source'])) ? $_POST['utm_source'] : '';
        $data['fields']['UTM_MEDIUM'] = (isset($_POST['utm_medium'])) ? $_POST['utm_medium'] : '';
        $data['fields']['UTM_CONTENT'] = (isset($_POST['utm_content'])) ? $_POST['utm_content'] : '';
        $data['fields']['UTM_CAMPAIGN'] = (isset($_POST['utm_campaign'])) ? $_POST['utm_campaign'] : '';
        $data['fields']['UTM_TERM'] = (isset($_POST['utm_term'])) ? $_POST['utm_term'] : '';

        $bitrixLead = CRest::call(
            'crm.lead.add',
            $data
        );
    }
} 
catch(Exception $e) 
{
    echo $e;
    errorLog($e);
}

// База данных
try 
{
    $isAuto = (isset($_POST['dmp'])) ? 1 : 0;

    $data = 
    [
        'bitrixId' => $bitrixLead['result'],
        'phone' => $_POST['phone'],
        'isAuto' => $isAuto
    ];

    $data['utmSource'] = (isset($_POST['utm_source'])) ? $_POST['utm_source'] : null;
    $data['utmMedium'] = (isset($_POST['utm_medium'])) ? $_POST['utm_medium'] : null;
    $data['utmContent'] = (isset($_POST['utm_content'])) ? $_POST['utm_content'] : null;
    $data['utmCampaign'] = (isset($_POST['utm_campaign'])) ? $_POST['utm_campaign'] : null;
    $data['utmTerm'] = (isset($_POST['utm_term'])) ? $_POST['utm_term'] : null;
    $data['domen'] = (isset($_POST['domen'])) ? $_POST['domen'] : null;

    $data['city'] = (isset($_POST['city'])) ? $citiesNames[$_POST['city']] : null;
    debug($data);
    insertData($conn, $data, 'lead');
}
catch(Exception $e)
{
    echo $e;
    errorLog($e);
}

// Телеграм
try 
{
    if ($_POST['source'] == 'website') {
        $utmSource = isset($_POST['utm_source']) ? $_POST['utm_source'] : null;
        $message = "";
        
        if (tryGetConnection($conn, $connection, $_POST['domen'], $utmSource))
        {
            $connection = $connection[0];
            $telegramInfo = readDataById($connection['telegramId'], $conn, 'telegram');
            $chat_id = $telegramInfo['chatId'];
            $message = $_POST['title'] . "\n";
        } 
        else 
        {
            $chat_id = TELEGRAM_TEST_CHAT_ID;
            $message = "Не была отправлена\n" . $_POST['title'] . "\n";
        }

        $message .= "Телефон: " . $_POST['phone'] . "\n";
        $message .= "Сайт: " . $_POST['domen'] . "\n";
        
        if ($utmSource != null && $utmSource != $_POST['domen'])
            $message .= "utm_source: " . $utmSource . "\n";

        foreach($_POST as $key => $value) 
        {
            if (str_contains($key, 'question') == false)
                continue;

            $message .= "  -$value\n";
        }

        $data = 
        [
            'chat_id' => $chat_id,
            'text' => $message
        ];

        $result = Request::sendMessage($data);
        debug($result);
    }
}
catch(Exception $e)
{
    echo $e;
    errorLog($e);
}
// Закрытие подключения
$conn = null;