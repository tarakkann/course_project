<?php
session_start();
require 'db_connection.php';

// Проверяем авторизацию
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// Получение текущих данных пользователя
$query = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Обработка изменений профиля
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    // Проверка на существующий email или username
    $check_query = "SELECT user_id FROM users WHERE (email = ? OR username = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ssi", $new_email, $new_username, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Имя пользователя или Email уже заняты.";
    } else {
        // Обновление данных пользователя
        $update_query = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($update_stmt->execute()) {
            $success = "Данные успешно обновлены.";
            $_SESSION['username'] = $new_username; // Обновляем сессионное имя пользователя
        } else {
            $error = "Ошибка обновления данных.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header id="main-header">
        <nav>
            <a href="logout.php"" class="main-button-link">
                    <button class="header-button">Выйти</button>
                </a> 
                <a href="map.html" class="main-button-link">
                    <button class="header-button">Карта</button>
                </a>   
        </nav>
    </header>
    <main>
        <h1>Профиль</h1>
        <p>Добро пожаловать, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
        <form method="POST" action="profile.php">
            <label for="username">Имя пользователя:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <button type="submit">Обновить</button>
        </form>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </main>
</body>
</html>
