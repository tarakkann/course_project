<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); 
    echo json_encode(['error' => 'Вы должны быть авторизованы, чтобы оставить отзыв.']);
    exit();
}
header('Content-Type: application/json');
require_once 'db_connection.php';

if (!isset($_POST['park_id'], $_POST['dog_type'], $_POST['rating'], $_POST['review_text'])) {
    echo json_encode(["error" => "Все поля обязательны для заполнения."]);
    exit;
}

$parkId = intval($_POST['park_id']);
$dogType = $_POST['dog_type'];
$rating = intval($_POST['rating']);
$reviewText = $_POST['review_text'];

$query = "INSERT INTO reviews (user_id, park_id, dog_type, rating, review_text) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iisss", $_SESSION['user_id'], $parkId, $dogType, $rating, $reviewText);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Отзыв успешно добавлен."]);
} else {
    echo json_encode(["error" => "Ошибка при добавлении отзыва."]);
}
?>
