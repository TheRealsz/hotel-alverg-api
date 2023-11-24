<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$id_cliente = $data['id_cliente'];
$numero_quarto = $data['numero_quarto'];
$data_entrada = $data['data_entrada'];
$data_saida = $data['data_saida'];
$forma_pagamento = $data['forma_pagamento'];

try {
    if (!$id_cliente || !$numero_quarto || !$data_entrada || !$forma_pagamento) {
        throw new Exception('Preencha todos os campos.');
    }

    $data_saida == "" ? $data_saida = "0000-00-00" : $data_saida = $data_saida;	

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        throw new Exception('Cliente não encontrado.');
    }

    if($cliente['hospedado']) {
        throw new Exception('Cliente já está hospedado.');
    }

    $nome_cliente = $cliente['nome'];

    $stmt = $conn->prepare("SELECT * FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero_quarto]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        throw new Exception('Quarto não encontrado.');
    }

    if ($data_entrada !== date('Y-m-d')) {
        throw new Exception('Data de entrada diferente da atual.');
    }

    if ($quarto['disponivel'] == 0) {
        throw new Exception('Quarto indisponível.');
    }

    $quarto_numero = $quarto['numero_quarto'];

    $stmt = $conn->prepare("INSERT INTO reservas (id_cliente, nome_cliente, numero_quarto, data_entrada, data_saida, forma_pagamento) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$id_cliente, $nome_cliente, $quarto_numero, $data_entrada, $data_saida, $forma_pagamento]);

    $stmt = $conn->prepare("UPDATE quartos SET disponivel = 0 WHERE numero_quarto = ?");
    $stmt->execute([$quarto_numero]);

    $stmt = $conn->prepare("UPDATE clientes SET hospedado = 1 WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva realizada com sucesso.'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao realizar reserva: " .$e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
