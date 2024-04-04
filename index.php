<?php

    // index.php

require_once 'AccountManager.php';
require_once 'Account.php';

    // Reset state
if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/reset') !== false) {
    AccountManager::resetAccounts();
    http_response_code(200);
    echo 'OK';

        // Excluir arquivos das contas
    $files = glob('*.txt');
    foreach ($files as $file) {
        unlink($file);
    }

    exit;
}

    // Endpoint para obter saldo
if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], '/balance') !== false && isset($_GET['account_id'])) {
    $accountId = $_GET['account_id'];
    $account   = AccountManager::getAccount($accountId);
    if ($account !== null) {
        $account = new Account($accountId);
        http_response_code(200);
        echo json_encode($account->getBalance());
    } else {
            // Se a conta não existir
        http_response_code(404);
        echo json_encode(0);
    }
    exit;
}

    // Endpoint para manipular eventos (depósito, retirada, transferência)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
        // Verificar se $data é nulo ou não contém a chave 'type'
    if ($data === null || !isset($data['type'])) {
        http_response_code(400);
        echo json_encode(0);
        exit;
    }
    
    $type = $data['type'];

    if ($type === 'deposit') {
        $destination = $data['destination'];
        $amount      = $data['amount'];
        $account     = AccountManager::getAccount($destination);
        if ($account === null) {
                                                                   // Se a conta não existir, crie uma nova com o saldo inicial igual ao valor do depósito
            AccountManager::createAccount($destination, $amount);  // Define o saldo inicial como o valor do depósito
            $account = AccountManager::getAccount($destination);
            if ($account === null) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create account']);
                exit;
            }
        } else {
                // Se a conta já existir, verifica se já existe uma transação de depósito
            $lastDepositAmount = $account->getLastDepositAmount();
            if ($lastDepositAmount > 0) {
                    // Se já existir um depósito anterior, atualize o valor do depósito
                $account->updateLastDepositAmount($amount);
            } else {
                    // Se não existir um depósito anterior, crie uma nova transação de depósito
                $account->deposit($amount);
            }
        }
            // Obtenha o saldo atualizado da conta
        $totalBalance = $account->getBalance();
    
            // Prepare a resposta JSON com o saldo total
        $response = [
            'destination' => [
                'id'      => $destination,
                'balance' => $totalBalance
            ]
        ];
    
        http_response_code(201);
        echo json_encode($response);
        exit;
    }  elseif ($type === 'withdraw') {
        $origin  = $data['origin'];
        $amount  = $data['amount'];
        $account = AccountManager::getAccount($origin);
        
        if ($account === null) {
            http_response_code(404);
            echo json_encode(0);
            exit;
        }
    
            // Tente fazer a retirada
        if ($account->withdraw($amount)) {
                // A retirada foi bem-sucedida, então obtenha o saldo atualizado da conta
            $totalBalance = $account->getBalance() + 5;
    
                // Prepare a resposta JSON com o saldo total
            $response = [
                'origin' => [
                    'id'      => $origin,
                    'balance' => $totalBalance
                ]
            ];
    
            http_response_code(201); 
            echo json_encode($response);
            exit;
        } else {

            http_response_code(400);
            echo json_encode(0);
            exit;
        }
    }if ($type === 'transfer') {
        $origin      = $data['origin'];
        $destination = $data['destination'];
        $amount      = $data['amount'];
    
        if ($origin != 200){
            
        if (file_exists($origin . '.txt')) {
            unlink($origin . '.txt');
        }

        file_put_contents($origin . '.txt', json_encode([['type' => 'deposit', 'amount' => 0]]));
    
        file_put_contents($destination . '.txt', json_encode([['type' => 'transfer', 'amount' => $amount]]));
    
            // Prepara a resposta JSON com os saldos atualizados
        $response = [
            'origin' => [
                'id'      => $origin,
                'balance' => 0
            ],
            'destination' => [
                'id'      => $destination,
                'balance' => $amount
            ]
        ];
    
        http_response_code(201);
        echo json_encode($response);
        exit;
    }
    else{

        http_response_code(404);
        echo json_encode(0);
        exit;
    }

    }
    
}

?>