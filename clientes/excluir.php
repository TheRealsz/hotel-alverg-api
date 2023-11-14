<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id = $data['id'];

$sql = "DELETE FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $id);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Cliente excluído com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao excluir o cliente.'
    ];
}

echo json_encode($response);

$conn = null;