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
    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->bindParam(1, $id_cliente);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        throw new Exception('Cliente não encontrado.');
    }

    if($cliente['hospedado']) {
        throw new Exception('Cliente já está hospedado.');
    }

    $nome_cliente = $cliente['nome'];

    $stmt = $conn->prepare("SELECT numero_quarto, disponivel FROM quartos WHERE numero_quarto = ?");
    $stmt->bindParam(1, $numero_quarto);
    $stmt->execute();
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
    $stmt->bindParam(1, $id_cliente);
    $stmt->bindParam(2, $nome_cliente);
    $stmt->bindParam(3, $quarto_numero);
    $stmt->bindParam(4, $data_entrada);
    $stmt->bindParam(5, $data_saida);
    $stmt->bindParam(6, $forma_pagamento);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE quartos SET disponivel = 0 WHERE numero_quarto = ?");
    $stmt->bindParam(1, $numero_quarto);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE clientes SET hospedado = 1 WHERE id_cliente = ?");
    $stmt->bindParam(1, $id_cliente);
    $stmt->execute();
    
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva realizada com sucesso.'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
