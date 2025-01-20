<?php
include 'db_connection.php';  // Подключаем файл с подключением к базе данных

// Получаем параметры фильтрации из GET-запроса
$dog_type = isset($_GET['dog_type']) ? $_GET['dog_type'] : '';
$rating_range = isset($_GET['rating_range']) ? $_GET['rating_range'] : '';

// Логируем параметры фильтрации для отладки
error_log("dog_type: $dog_type, rating_range: $rating_range");

// Формируем SQL-запрос
$sql = "SELECT DISTINCT dp.global_id, dp.Location, dp.latitude, dp.longitude
        FROM dog_parks dp
        LEFT JOIN reviews r ON dp.global_id = r.park_id WHERE 1=1";

// Добавляем фильтрацию по типу собаки
if (!empty($dog_type)) {
    $sql .= " AND r.dog_type = '$dog_type'";
}

// Добавляем фильтрацию по рейтингу
if (!empty($rating_range)) {
    $range = explode('-', $rating_range);
    $min_rating = $range[0];
    $max_rating = isset($range[1]) ? $range[1] : $min_rating;
    $sql .= " AND r.rating BETWEEN $min_rating AND $max_rating";
}

// Логируем SQL-запрос
error_log("SQL запрос: $sql");

// Выполняем запрос
$result = $conn->query($sql);

// Массив для хранения данных о парках
$parks = [];
while($row = $result->fetch_assoc()) {
    $parks[] = $row;
}

// Логируем количество найденных парков
error_log("Найдено парков: " . count($parks));

// Отправляем данные в формате JSON
header('Content-Type: application/json');
echo json_encode($parks);
?>
