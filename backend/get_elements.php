<?php
include 'db_connection.php';

header('Content-Type: application/json');

$query = "SELECT DISTINCT Elements FROM dogparks";
$result = $conn->query($query);

$unique_elements = [];
while ($row = $result->fetch_assoc()) {
    $elements = preg_split('/,\\s*/', trim($row['Elements'], '[]'));
    foreach ($elements as $element) {
        if (!in_array($element, $unique_elements)) {
            $unique_elements[] = $element;
        }
    }
}

echo json_encode($unique_elements);
$conn->close();
?>
