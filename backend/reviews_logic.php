<?php
require_once 'db_connection.php';

$park_id = isset($_GET['park_id']) ? intval($_GET['park_id']) : null;
$response = [];

if ($park_id) {
    $parkQuery = "SELECT * FROM dogparks WHERE global_id = ?";
    $stmt = $conn->prepare($parkQuery);
    $stmt->bind_param("i", $park_id);
    $stmt->execute();
    $parkResult = $stmt->get_result();
    $response['park'] = $parkResult->num_rows > 0 ? $parkResult->fetch_assoc() : null;

    $reviewsQuery = "SELECT * FROM reviews WHERE park_id = ?";
    $stmt = $conn->prepare($reviewsQuery);
    $stmt->bind_param("i", $park_id);
    $stmt->execute();
    $reviewsResult = $stmt->get_result();

    $reviews = [];
    while ($review = $reviewsResult->fetch_assoc()) {
        $reviews[] = $review;
    }
    $response['reviews'] = $reviews;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
