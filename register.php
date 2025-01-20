<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Проверка на существующий username
    $checkUsernameQuery = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUsernameQuery);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: register.html?error=username_exists");
        exit();
    }

    // Проверка на существующий email
    $checkEmailQuery = "SELECT user_id FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkEmailQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        header("Location: register.html?error=email_exists");
        exit();
    }

    // Добавление нового пользователя
    $insertQuery = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['user_id'] = $conn->insert_id;
        header("Location: profile.php");
        exit();
    } else {
        header("Location: register.html?error=failed");
        exit();
    }
}
?>
