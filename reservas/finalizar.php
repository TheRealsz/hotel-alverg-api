<?php

require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

$id_reserva = $data['id_reserva'];
$data_saida = $data['data_saida'];

try {

    if (!$id_reserva) {
        throw new Exception('ID da reserva não informado.');
    }

    if (!$data_saida) {
        throw new Exception('Data de saída não informada.');
    }

    if ($data_saida !== date('Y-m-d')) {
        throw new Exception('Data de saída inválida.');
    }

    $stmt = $conn->prepare("SELECT * FROM reservas WHERE id_reserva = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reserva) {
        throw new Exception('Reserva não encontrada.');
    }

    if ($reserva['status'] == 0) {
        throw new Exception('Reserva já finalizada.');
    }

    $stmt = $conn->prepare("UPDATE clientes SET hospedado = 0 WHERE id_cliente = ?");
    $stmt->execute([$reserva['id_cliente']]);

    $stmt = $conn->prepare("UPDATE quartos SET disponivel = 1 WHERE numero_quarto = ?");
    $stmt->execute([$reserva['numero_quarto']]);

    $stmt = $conn->prepare("UPDATE reservas SET data_saida = ?, status = 0 WHERE id_reserva = ?");
    $stmt->execute([$data_saida, $id_reserva]);

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Reserva finalizada com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao finalizar reserva: " . $e->getMessage()
    ];
}

echo json_encode($response);
$conn = null;