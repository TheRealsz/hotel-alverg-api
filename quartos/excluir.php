<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];

$sql = "SELECT * FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);
$stmt->execute();
$quarto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quarto) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Quarto não encontrado.'
    ];
    echo json_encode($response);
    exit;
}

if ($quarto['disponivel'] == 0) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Não é possível excluir um quarto ocupado.'
    ];
    echo json_encode($response);
    exit;
}

$sql = "DELETE FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Quarto excluído com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao excluir o quarto.'
    ];
}

echo json_encode($response);

$conn = null;