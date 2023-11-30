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
        http_response_code(400);
        throw new Exception('Reserva não encontrada.');
    }

    if($reserva['status'] == 0) {
        http_response_code(400);
        throw new Exception('Reserva já finalizada.');
    }

    $stmt = $conn->prepare("UPDATE reservas SET data_saida = ?, forma_pagamento = ? WHERE id_reserva = ?");
    $stmt->execute([$data_saida, $forma_pagamento, $id_reserva]);

    $response = [
        'success' => true,
        'message' => 'Reserva editada com sucesso!'
    ];

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao editar reserva: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;