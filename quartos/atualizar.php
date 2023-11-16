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

if(!$quarto) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Quarto não encontrado.'
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

$disponivel = $quarto['disponivel'];



$sql = "UPDATE quartos SET capacidade = ?, valor_diaria = ?, disponivel = ? WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $capacidade);
$stmt->bindParam(2, $diaria);
$stmt->bindParam(3, $disponivel);
$stmt->bindParam(4, $numero);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Quarto atualizado com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao atualizar o quarto.'
    ];
}

echo json_encode($response);

$conn = null;