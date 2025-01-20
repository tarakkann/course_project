<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403); // Доступ запрещен
    echo json_encode(['error' => 'Вы должны быть авторизованы, чтобы оставить отзыв.']);
    exit();
}
header('Content-Type: application/json');
require_once 'db_connection.php';

// Проверка наличия данных
if (!isset($_POST['park_id'], $_POST['dog_type'], $_POST['rating'], $_POST['review_text'])) {
    echo json_encode(["error" => "Все поля обязательны для заполнения."]);
    exit;
}

// Получение данных из POST-запроса
$parkId = intval($_POST['park_id']);
$dogType = $_POST['dog_type'];
$rating = intval($_POST['rating']);
$reviewText = $_POST['review_text'];

// Вставка отзыва в базу данных
$query = "INSERT INTO reviews (park_id, dog_type, rating, review_text) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("isis", $parkId, $dogType, $rating, $reviewText);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => "Отзыв успешно добавлен."]);
} else {
    echo json_encode(["error" => "Ошибка при добавлении отзыва."]);
}

$conn->close();
?>
