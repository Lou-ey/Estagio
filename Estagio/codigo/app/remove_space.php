<?php
include 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $spaceId = $_POST["space_id"];

    $sql = "DELETE FROM espacos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $spaceId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}

$conn->close();
?>

