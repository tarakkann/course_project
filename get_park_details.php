<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

// Проверка наличия ID
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Не указан ID площадки"]);
    exit;
}

$globalId = intval($_GET['id']);

// Запрос данных о площадке
$query = "SELECT * FROM dog_parks WHERE global_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $globalId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $park = $result->fetch_assoc();

    // Проверка, если элементы - это строка, а не JSON, то разбиваем через запятую
    $elements = null;
    if ($park['Elements']) {
        // Применяем регулярное выражение для удаления скобок и разделяем строку на массив
        $elements = preg_replace('/[\[\]]/', '', $park['Elements']); // Убираем квадратные скобки
        $elements = preg_split('/,\s*/', $elements); // Разделяем строку по запятой и пробелу
    }

    // Формирование ответа
    $response = [
        "id" => $park['global_id'],
        "address" => $park['Location'],
        "adm_area" => $park['AdmArea'],
        "district" => $park['District'],
        "area" => $park['DogParkArea'],
        "elements" => $elements,
        "lighting" => $park['Lighting'],
        "fencing" => $park['Fencing'],
        "latitude" => $park['latitude'],
        "longitude" => $park['longitude']
    ];
    echo json_encode($response);
} else {
    echo json_encode(["error" => "Площадка не найдена"]);
}

$conn->close();
?>
