<?php
require_once '../config.php';


try{
    // Define a chave e o método de criptografia
    $key = 'chave-secreta';
    $method = 'aes-256-cbc';
    $sql = "SELECT * FROM clientes";
    $stmt = $conn->query($sql);
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Descriptografe o CPF de cada cliente
    foreach ($clientes as $i => $cliente) {
        $iv = $cliente['iv']; // Supondo que você está armazenando o vetor de inicialização no banco de dados
        $encrypted_cpf = $cliente['cpf'];

        // Descriptografe o CPF
        $decrypted_cpf = openssl_decrypt($encrypted_cpf, $method, $key, OPENSSL_RAW_DATA, $iv);

        // Substitua o CPF criptografado pelo CPF descriptografado
        $clientes[$i]['cpf'] = $decrypted_cpf;
    }

    $response = [
        'success' => true,
        'data' => $clientes
    ];

} catch(Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage() || 'Erro ao buscar clientes.'
    ];
}

echo json_encode($response);

$conn = null;