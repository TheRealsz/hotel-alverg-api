<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$nome = $data['nome'];
$cpf = $data['cpf'];
$email = $data['email'];
$fone = $data['fone'];

try {
    if(!$nome || !$cpf || !$email || !$fone) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        throw new Exception('CPF já cadastrado.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        throw new Exception('E-mail já cadastrado.');
    }

    $stmt = $conn->prepare("INSERT INTO clientes (nome, cpf, email, telefone, hospedado) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$nome, $cpf, $email, $fone]);

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Cliente cadastrado com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao cadastrar cliente: " .$e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
