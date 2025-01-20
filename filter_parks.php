<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$dogType = $_GET['dog_type'] ?? '';
$ratingRange = $_GET['rating'] ?? '';

$ratingConditions = [
    "1-2" => "rating BETWEEN 1 AND 2",
    "3-4" => "rating BETWEEN 3 AND 4",
    "5" => "rating = 5"
];

$query = "SELECT DISTINCT dp.global_id, dp.latitude, dp.longitude FROM dog_parks dp
          JOIN reviews r ON dp.global_id = r.park_id WHERE 1=1";

$params = [];
$types = "";

if (!empty($dogType)) {
    $query .= " AND r.dog_type = ?";
    $params[] = $dogType;
    $types .= "s";
}

if (!empty($ratingRange) && isset($ratingConditions[$ratingRange])) {
    $query .= " AND " . $ratingConditions[$ratingRange];
}

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$parks = [];

while ($row = $result->fetch_assoc()) {
    $parks[] = $row;
}

echo json_encode($parks);
?>
