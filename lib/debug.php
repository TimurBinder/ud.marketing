<?php

function debug(array|string $array): void
{
    if (DEBUG)
        echo "<pre>" . print_r($array, true) . "</pre>";
}

function isJson(string $string): bool
{
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function errorLog(string $message): void
{
    $message = "Error || ". date('d-m-Y H:i:s') . " || $message\n";

    $path = LOG . '/' . date('Y');

    if(!is_dir($path))
        mkdir($path, 0777, true);

    $path = LOG . '/' . date('Y') . '/' . date('m');

    if(!is_dir($path))
        mkdir($path, 0777, true);

    $path = LOG . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '.log';

    file_put_contents($path, $message, FILE_APPEND);
}

function my_error_handler($errno, $errstr, $errfile, $errline): void
{
    if (!(error_reporting() & $errno))
        return;

    // Запись ошибки в журнал
    errorLog("[$errno] $errstr in $errfile:$errline");

    if (DEBUG)
        echo "An error occurred. Please try again later.";
}

function my_shutdown_handler(): void
{
    $error = error_get_last();

    if ($error !== null) 
    {
        // Запись ошибки в журнал
        errorLog($error['message'] . " in " . $error['file'] . ":" . $error['line']);

        if (DEBUG)
            echo "An error occurred. Please try again later.";
    }
}