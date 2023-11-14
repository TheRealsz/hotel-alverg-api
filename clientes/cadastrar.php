<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$nome = $data['nome'];
$cpf = $data['cpf'];
$email = $data['email'];
$fone = $data['fone'];
$hosted = $data['hosted'];

$sql = "INSERT INTO clientes (nome, cpf, email, telefone, hospedado) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $nome);
$stmt->bindParam(2, $cpf);
$stmt->bindParam(3, $email);
$stmt->bindParam(4, $fone);
$stmt->bindParam(5, $hosted);
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
