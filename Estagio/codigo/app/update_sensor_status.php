<?php
include 'config.php';

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Receber os parâmetros do sensor ID e novo status
$sensorId = $_POST['sensor_id'];
$newStatus = $_POST['new_status'];

// Obter a data atual no formato ISO 8601
$newUpdatedAt = date("Y-m-d H:i:s");

// Preparar a consulta SQL com um prepared statement
$sql = "UPDATE sensors SET status=?, updated_at=? WHERE id=?";

// Preparar a declaração
$stmt = $conn->prepare($sql);

// Verificar se a preparação da consulta falhou
if ($stmt === false) {
    die("Prepare falhou: " . $conn->error);
}

// Vincular os parâmetros e executar a consulta
$stmt->bind_param("ssi", $newStatus, $newUpdatedAt, $sensorId);

// Executar a consulta
if ($stmt->execute() === false) {
    die("Execute falhou: " . $stmt->error);
}

// Fechar a instrução e a conexão
$stmt->close();
$conn->close();

// Retorna uma resposta JSON de sucesso
$response = array('success' => true);
echo json_encode($response);
?>

