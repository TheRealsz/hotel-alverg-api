<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$nome = $data['nome'];
$cpf = $data['cpf'];
$email = $data['email'];
$fone = $data['fone'];

try {
    if (!$nome || !$cpf || !$email || !$fone) {
        throw new Exception('Preencha todos os campos.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        http_response_code(400);
        throw new Exception('CPF já cadastrado.');
    }

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ?");
    $stmt->execute([$email]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        http_response_code(400);
        throw new Exception('E-mail já cadastrado.');
    }

    $cpf = trim($cpf);
    $cpf = str_replace(array('-', ' '), '', $cpf);

    if(strlen($cpf) != 11) {
        http_response_code(400);
        throw new Exception('O CPF deve ser preenchido no padrão 000.000.000-00.');
    }
    
    $fone = preg_replace("/[^0-9]/", "", $fone);
    if(strlen($fone) != 11) {
        http_response_code(400);
        throw new Exception('O telefone deve ser preenchido no padrão (00) 00000-0000.');
    }

    $stmt = $conn->prepare("INSERT INTO clientes (nome, cpf, email, telefone, hospedado) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([$nome, $cpf, $email, $fone]);

    $response = [
        'success' => true,
        'message' => 'Cliente cadastrado com sucesso!'
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => "Erro ao cadastrar cliente: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
