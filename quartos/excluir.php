<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];

$sql = "DELETE FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);

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