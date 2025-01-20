<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// Получение параметров фильтра
$dogType = isset($_GET['dog_type']) ? $_GET['dog_type'] : null;

// Фильтрация отзывов
$query = "SELECT park_id, AVG(rating) as avg_rating FROM reviews";
if ($dogType) {
    $query .= " WHERE dog_type = ?";
}
$query .= " GROUP BY park_id";

$stmt = $conn->prepare($query);
if ($dogType) {
    $stmt->bind_param("s", $dogType);
}
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

echo json_encode($reviews);
$conn->close();
?>
