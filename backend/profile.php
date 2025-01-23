<?php
session_start();
require 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /frontend/login.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_username = trim($_POST['username']);
    $new_email = trim($_POST['email']);

    $check_query = "SELECT user_id FROM users WHERE (email = ? OR username = ?) AND user_id != ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ssi", $new_email, $new_username, $user_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $error = "Имя пользователя или Email уже заняты.";
    } else {
        $update_query = "UPDATE users SET username = ?, email = ? WHERE user_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($update_stmt->execute()) {
            $success = "Данные успешно обновлены.";
            $_SESSION['username'] = $new_username; // обновление сессионного имени польз-ля
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
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<style>
    main {
    text-align: center;
    padding: 20px;
    position: relative;
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
</style>
<body>
<header>
    <div class="logo">
        <img src="../assets/images/Logo.svg" alt="PetMap Logo">
        <span class="site-name">PetMap</span>
    </div>

    <div class="header-nav">
        <a href="/frontend/index.html" class="header-button">Главная</a>
        <a href="/frontend/map.html" class="header-button">Карта</a>
        <a href="/backend/profile.php" class="header-button active">Профиль</a>
        <a href="/frontend/reviews_page.html" class="header-button">Отзывы</a>
    </div>

    <div class="header-buttons">
        <a href="/backend/logout.php" class="header-button">Выход</a>
    </div>
</header>

    <main id="profile-page">
        <h1>Редактировать профиль</h1>
        <p>Добро пожаловать, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
        <form id="profile-form" method="POST" action="profile.php">
        <div class="form-group">
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <label for="username"> Имя пользователя</label>
        </div>
        
        <div class="form-group">
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label> email@example.com</label>
        </div>

            <button type="submit">Отредактировать</button>
        </form>
        <?php if (isset($success)): ?>
            <p style="color: green;"><?php echo $success; ?></p>
        <?php elseif (isset($error)): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </main>

    <footer>
        PetMap © 2025. Все права защищены.
        С использование открытых данных:  &nbsp;<p><a href="https://data.mos.ru/opendata/2663?isDynamic=false">  ссылка на датасет</a></p>
    </footer>

</body>
</html>
