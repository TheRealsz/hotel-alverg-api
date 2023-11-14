<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$id = $data['id'];
$nome = $data['nome'];
$email = $data['email'];
$fone = $data['fone'];
$cpf = $data['cpf'];
$hosted = $data['hosted'];

$sql = "UPDATE clientes SET nome = ?, email = ?, telefone = ?, cpf = ?, hospedado = ? WHERE id_cliente = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $nome);
$stmt->bindParam(2, $email);
$stmt->bindParam(3, $fone);
$stmt->bindParam(4, $cpf);
$stmt->bindParam(5, $hosted);
$stmt->bindParam(6, $id);

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