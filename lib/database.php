<?php

function checkForInjection(array $data, PDO $conn): bool
{   
    $pattern = '/^[a-zA-Z0-9_\-]+$/';
    $result = true;

    foreach($data as $item)
        $result = (bool)preg_match($pattern, $item);

    return $result;
}

function insertData(mysqli $conn, array $data, string $table): void
{
    // Получение имен столбцов и значений из массива
    $columnNames = array_keys($data);
    $columnValues = array_values($data);

    // Подготовка SQL-запроса для вставки данных
    $sql = "INSERT INTO `{$table}` (`" . implode("`, `", $columnNames) . "`) VALUES (?" . str_repeat(", ?", count($columnValues) - 1) . ")";
    $stmt = $conn->prepare($sql);

    // Привязка значений к параметрам
    $stmt->bind_param(str_repeat("s", count($columnValues)), ...$columnValues);

    debug($stmt->get_result());

    // Выполнение SQL-запроса
    if ($stmt->execute()) {
    } else {
        echo "Error: " . $sql . "<br>" . $stmt->error;
    }
}

function readData(array $conditions, mysqli $conn, string $table): array
{
    $conditionStrings = [];
    $params = [];
    $types = '';

    foreach ($conditions as $column => $value) {
        $conditionStrings[] = "$column = ?";
        $params[] = &$conditions[$column];
        $types .= 's';
    }

    $conditionString = implode(' AND ', $conditionStrings);
    $sql = "SELECT * FROM $table WHERE $conditionString";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $data;
}

function readDataById(int $id, mysqli $conn, string $table): array
{
    $sql = "SELECT * FROM $table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $data;
}

function tryGetSource(mysqli $conn, array|null &$source, string $domen, string $utmSource = null): bool 
{
    $sourceConditions = [ 'domen' => $domen ];

    if ($utmSource !== null)
        $sourceConditions['utm'] = $utmSource;

    $source = readData($sourceConditions, $conn, 'source');

    return !empty($source);
}

function tryGetConnection(mysqli $conn, array|null &$connection, string $domen, string $utmSource = null): bool
{
    if (tryGetSource($conn, $source, $domen, $utmSource) == false)
    {
        if (tryGetSource($conn, $source, $domen, '') == false) 
            return false;
    }

    $source = $source[0];

    $connectionCondition = [ 'sourceId' => $source['id'] ];
    $connection = readData($connectionCondition, $conn, 'connection');

    return !empty($connection);
}