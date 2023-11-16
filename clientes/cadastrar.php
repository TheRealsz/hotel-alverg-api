<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$nome = $data['nome'];
$cpf = $data['cpf'];
$email = $data['email'];
$fone = $data['fone'];

$sql = "SELECT * FROM clientes WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $cpf);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cliente) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'CPF já cadastrado.'
    ];
    echo json_encode($response);
    exit;
}

$sql = "SELECT * FROM clientes WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $email);
$stmt->execute();
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if($cliente) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'E-mail já cadastrado.'
    ];
    echo json_encode($response);
    exit;
}

$sql = "INSERT INTO clientes (nome, cpf, email, telefone, hospedado) VALUES (?, ?, ?, ?, 0)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $nome);
$stmt->bindParam(2, $cpf);
$stmt->bindParam(3, $email);
$stmt->bindParam(4, $fone);
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
