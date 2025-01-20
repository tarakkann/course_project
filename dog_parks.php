<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require_once 'db_connection.php';

// Извлечение всех площадок с глобальным ID и координатами
$query = "SELECT global_id, latitude, longitude FROM dog_parks WHERE latitude IS NOT NULL AND longitude IS NOT NULL LIMIT 10";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $parks = [];
    while ($row = $result->fetch_assoc()) {
        $parks[] = [
            "global_id" => $row['global_id'],
            "latitude" => $row['latitude'],
            "longitude" => $row['longitude'],
        ];
    }
    echo json_encode($parks);
} else {
    echo json_encode([]);
}

$conn->close();
?>
