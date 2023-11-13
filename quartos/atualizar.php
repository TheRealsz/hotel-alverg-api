<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$numero = $data['numero'];
$capacidade = $data['capacidade'];
$diaria = $data['diaria'];
$disponivel = $data['disponivel'];


$sql = "UPDATE quartos SET capacidade = ?, diaria = ?, disponivel = ? WHERE numero = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $capacidade);
$stmt->bindParam(2, $diaria);
$stmt->bindParam(3, $disponivel);
$stmt->bindParam(4, $numero);

if ($stmt->execute()) {
    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Cliente atualizado com sucesso!'
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao atualizar o cliente.'
    ];
}

echo json_encode($response);

$conn = null;