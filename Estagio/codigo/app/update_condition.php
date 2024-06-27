<?php
include 'config.php';

// Verifica se os dados estão presentes no array $_POST
if(isset($_POST['id'], $_POST['operator'], $_POST['value'])) {
    $id = $_POST['id'];
    $operator = $_POST['operator'];
    $value = $_POST['value'];

    $sql = "UPDATE alertconditions SET condition_type = ?, value = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdi", $operator, $value, $id);

    $response = [];
    if ($stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['error'] = $stmt->error;
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['error'] = "Dados ausentes";
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>

