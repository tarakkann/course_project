<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Проверка на существующий email
    $query = "SELECT user_id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Проверка пароля
        if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location:  /backend/profile.php");
            exit();
        } else {
            // Неверный пароль
            header("Location: /frontend/login.html?error=invalid_password");
            exit();
        }
    } else {
        // Email не найден
        header("Location:  /frontend/login.html?error=user_not_found");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
