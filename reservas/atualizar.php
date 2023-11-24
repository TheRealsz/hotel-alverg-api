<?php

require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$id_reserva = $data['id_reserva'];
$data_saida = $data['data_saida'];
$forma_pagamento = $data['forma_pagamento'];

try {

    if(!$id_reserva) {
        throw new Exception('ID da reserva não informado.');
    }

    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$reserva) {
        throw new Exception('Reserva não encontrada.');
    }

    $cliente = $reserva['id_cliente'];

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("UPDATE reservas SET data_saida = ?, forma_pagamento = ? WHERE id_reserva = ?");
    $stmt->execute([$data_saida, $forma_pagamento, $id_reserva]);

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva editada com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao editar reserva: " . $e->getMessage()
    ];
}
