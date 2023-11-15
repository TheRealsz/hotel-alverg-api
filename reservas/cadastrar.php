<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$id_cliente = $data['id_cliente'];
$numero_quarto = $data['numero_quarto'];
$data_entrada = $data['data_entrada'];
$data_saida = $data['data_saida'];
$forma_pagamento = $data['forma_pagamento'];

$sql = "SELECT nome FROM clientes WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $id_cliente);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    $response = [
        'success' => false,
        'status' => 400,
        'message' => 'Cliente não encontrado.'
    ];
    echo json_encode($response);
    exit;
}

$nome_cliente = $cliente['nome'];


$sql = "SELECT numero_quarto FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero_quarto);
$stmt->execute();
$quarto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quarto) {
    $response = [
        'success' => false,
        'status' => 400,
        'message' => 'Quarto não encontrado.'
    ];
    echo json_encode($response);
    exit;
} 

$sql = "SELECT disponivel FROM quartos WHERE numero_quarto = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $numero_quarto);
$stmt->execute();
$disponivel = $stmt->fetch(PDO::FETCH_ASSOC);

if ($disponivel == 0) {
    $response = [
        'success' => false,
        'status' => 400,
        'message' => 'Quarto indisponível.'
    ];
    echo json_encode($response);
    exit;
}

$quarto = $quarto['numero_quarto'];

$sql = "INSERT INTO reservas (id_cliente, nome_cliente, numero_quarto, data_entrada, data_saida, forma_pagamento) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $id_cliente);
$stmt->bindParam(2, $nome_cliente);
$stmt->bindParam(3, $quarto);
$stmt->bindParam(4, $data_entrada);
$stmt->bindParam(5, $data_saida);
$stmt->bindParam(6, $forma_pagamento);
if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva feita com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao realizar a reserva.'
    ];
}

echo json_encode($response);

$conn = null;
