<?php
require_once '../config.php';

$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);
$nome = $data['nome'];
$email = $data['email'];
$fone = $data['fone'];
$cpf = $data['cpf'];
$hosted = $data['hosted'];

// Inserir os dados do formulário no banco de dados
$sql = "INSERT INTO clientes (nome, email, telefone, cpf, hospedado) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(1, $nome);
$stmt->bindParam(2, $email);
$stmt->bindParam(3, $fone);
$stmt->bindParam(4, $cpf);
$stmt->bindParam(5, $hosted);
$stmt->execute();

// Fechar a conexão com o banco de dados
$conn = null;

// Enviar uma mensagem de sucesso para o usuário
echo json_encode([
    'success' => true,
    'message' => 'Cliente cadastrado com sucesso!'
]);
