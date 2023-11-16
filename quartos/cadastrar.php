<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];
$capacidade = $data['capacidade'];
$diaria = $data['diaria'];

$sql = "SELECT * FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);
$stmt->execute();
$quarto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($quarto) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Número de quarto já cadastrado.'
    ];
    echo json_encode($response);
    exit;
}

if ($capacidade < 1 || $capacidade > 5) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Capacidade inválida.'
    ];
    echo json_encode($response);
    exit;
}


$sql = "INSERT INTO quartos (numero_quarto, capacidade, valor_diaria, disponivel) VALUES (?, ?, ?, 1)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);
$stmt->bindParam(2, $capacidade);
$stmt->bindParam(3, $diaria);
if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Quarto cadastrado com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao cadastrar o quarto.'
    ];
}

echo json_encode($response);

$conn = null;
