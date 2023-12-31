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

    $data_saida == "" ? $data_saida = "" : $data_saida = $data_saida;	

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cliente) {
        http_response_code(400);
        throw new Exception('Cliente não encontrado.');
    }

    if($cliente['hospedado']) {
        http_response_code(400);
        throw new Exception('Cliente já está hospedado.');
    }

    $nome_cliente = $cliente['nome'];

    $stmt = $conn->prepare("SELECT * FROM quartos WHERE numero_quarto = ?");
    $stmt->execute([$numero_quarto]);
    $quarto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quarto) {
        http_response_code(400);
        throw new Exception('Quarto não encontrado.');
    }

    if($data_entrada != date('Y-m-d')) {
        http_response_code(400);
        throw new Exception('Data de entrada diferente da data de hoje.');
    }

    if ($data_saida !== "" && $data_entrada > $data_saida) {
        http_response_code(400);
        throw new Exception('Data de entrada não pode ser maior que a data de saída.');
    }

    if ($quarto['disponivel'] == 0) {
        http_response_code(400);
        throw new Exception('Quarto indisponível.');
    }

    $quarto_numero = $quarto['numero_quarto'];

    $stmt = $conn->prepare("INSERT INTO reservas (id_cliente, nome_cliente, numero_quarto, data_entrada, data_saida, forma_pagamento, status) VALUES (?, ?, ?, ?, ?, ?, 1)");
    $stmt->execute([$id_cliente, $nome_cliente, $quarto_numero, $data_entrada, $data_saida, $forma_pagamento]);

    $stmt = $conn->prepare("UPDATE quartos SET disponivel = 0 WHERE numero_quarto = ?");
    $stmt->execute([$quarto_numero]);

    $stmt = $conn->prepare("UPDATE clientes SET hospedado = 1 WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    
    $response = [
        'success' => true,
        'message' => 'Reserva realizada com sucesso.'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao realizar reserva: " .$e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
