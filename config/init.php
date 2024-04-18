<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

define('ROOT', __DIR__ . '/..');
define('CONFIG', ROOT . '/config');
define('CORE', ROOT . '/core');
define('LOG', CORE . '/log');

define('DEBUG', true);
define('RUN', true);

define('TELEGRAM_BOT_TOKEN', '5858304349:AAFfh6hWfDJJa6AtsZNnH5rk-n4MLJ-scfI');
define('TELEGRAM_BOT_NAME', 't.stomadentbot');
define('TELEGRAM_TEST_CHAT_ID', '-4003221145');

define('C_REST_WEB_HOOK_URL','https://ud-rus.ru/rest/1/v481czf7130tqs6h/');//url on creat Webhook
define('C_REST_CURRENT_ENCODING','utf-8');
define('C_REST_IGNORE_SSL',true);//turn off validate ssl by curl
define('C_REST_LOG_TYPE_DUMP',true); //logs save var_export for viewing convenience
define('C_REST_BLOCK_LOG',true);//turn off default logs
define('C_REST_LOGS_DIR', LOG); //directory path to save the log