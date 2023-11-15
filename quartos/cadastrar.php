<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];
$capacidade = $data['capacidade'];
$diaria = $data['diaria'];
$disponivel = $data['disponivel'];

$sql = "INSERT INTO quartos (numero_quarto, capacidade, valor_diaria, disponivel) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero);
$stmt->bindParam(2, $capacidade);
$stmt->bindParam(3, $diaria);
$stmt->bindParam(4, $disponivel);
if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Cliente cadastrado com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao cadastrar o cliente.'
    ];
}

echo json_encode($response);

$conn = null;
