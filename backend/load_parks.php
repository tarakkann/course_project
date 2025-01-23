<?php
require_once 'db_connection.php';

$query = "SELECT global_id, Location FROM dogparks";
$result = $conn->query($query);

$parks = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $parks[] = [
            'global_id' => $row['global_id'],
            'Location' => $row['Location']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($parks);
?>
