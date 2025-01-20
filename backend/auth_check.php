<?php
session_start();

header('Content-Type: application/json');

// Проверяем, авторизован ли пользователь
if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'is_logged_in' => true,
        'username' => $_SESSION['username']
    ]);
} else {
    echo json_encode(['is_logged_in' => false]);
}
?>
