<?php
header('Content-Type: application/json');
require_once 'db_connection.php'; 

$parkId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($parkId > 0) {
    $query = "SELECT rating, review_text, dog_type FROM reviews WHERE park_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $parkId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reviews = [];

    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }

    echo json_encode($reviews);
} else {
    echo json_encode(["error" => "Invalid park ID"]);
}
?>
