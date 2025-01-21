<?php
include 'db_connection.php';  

$dog_type = isset($_GET['dog_type']) ? $_GET['dog_type'] : '';
$rating_range = isset($_GET['rating_range']) ? $_GET['rating_range'] : '';

error_log("dog_type: $dog_type, rating_range: $rating_range");

$sql = "SELECT DISTINCT dp.global_id, dp.Location, dp.latitude, dp.longitude
        FROM dog_parks dp
        LEFT JOIN reviews r ON dp.global_id = r.park_id WHERE 1=1";

if (!empty($dog_type)) {
    $sql .= " AND r.dog_type = '$dog_type'";
}

if (!empty($rating_range)) {
    $range = explode('-', $rating_range);
    $min_rating = $range[0];
    $max_rating = isset($range[1]) ? $range[1] : $min_rating;
    $sql .= " AND r.rating BETWEEN $min_rating AND $max_rating";
}

error_log("SQL запрос: $sql");

$result = $conn->query($sql);

$parks = [];
while($row = $result->fetch_assoc()) {
    $parks[] = $row;
}

error_log("Найдено парков: " . count($parks));

header('Content-Type: application/json');
echo json_encode($parks);
?>
