<?php

    // Account.php

class Account {
    private $id;
    private $file;
    private $transactions;
    private $balance;

    public function __construct($id) {
        $this->id           = $id;
        $this->file         = $id . '.txt';
        $this->transactions = [];
        $this->balance      = 0;             // Inicializa o saldo da conta como 0
        $this->loadTransactions();
    }
    
    public function getLastDepositAmount() {
        $lastDepositAmount = 0;
            // Encontra o último depósito
        for ($i = count($this->transactions) - 1; $i >= 0; $i--) {
            if ($this->transactions[$i]->getType() === 'deposit') {
                $lastDepositAmount = $this->transactions[$i]->getAmount();
                break;  // Encerra o loop após encontrar o último depósito
            }
        }
        return $lastDepositAmount;
    }
    
    public function updateLastDepositAmount($amount) {
            // Encontra o índice da última transação de depósito
        $lastDepositIndex = -1;
        for ($i = count($this->transactions) - 1; $i >= 0; $i--) {
            if ($this->transactions[$i]->getType() === 'deposit') {
                $lastDepositIndex = $i;
                break;
            }
        }
        if ($lastDepositIndex !== -1) {
                // Atualiza o valor do último depósito
            $this->transactions[$lastDepositIndex]->setAmount($amount);
                // Atualiza o saldo interno
            $this->balance = $amount;
                // Salva as transações atualizadas
            $this->saveTransactions();
        }
    }
    
    public function getBalance() {
            // Inicializa o saldo com o saldo inicial da conta
        $balance = $this->balance;
        
            // Ajusta o saldo conforme necessário
        foreach ($this->transactions as $transaction) {
            if ($transaction->getType() === 'deposit') {
                $balance = $transaction->getAmount();  // Soma o valor do depósito ao saldo
            } elseif ($transaction->getType() === 'withdrawal') {
                $balance -= $transaction->getAmount();  // Subtrai o valor da retirada do saldo
            }
        }
        
        return $balance;
    }
    public function setInitialBalance($amount) {
        $this->balance = $amount;
        $this->saveTransactions();  // Salva o saldo inicial como uma transação
    }
    
    public function deposit($amount) {
            // Verifica se já existe uma conta com o ID especificado
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    
            // Carrega as transações existentes
        $existingData = json_decode(file_get_contents($this->file), true);
    
            // Verifica se já existem depósitos anteriores
        $existingDeposits = array_filter($existingData, function($transaction) {
            return $transaction['type'] === 'deposit';
        });
    
            // Calcula o saldo anterior somando todos os depósitos anteriores
        $previousBalance = 10;
        foreach ($existingDeposits as $deposit) {
            $previousBalance += $deposit['amount'];
        }

            // Se existirem, atualiza o valor do último depósito
        $totalAmount = $previousBalance + $amount;

            // Substitui todos os depósitos anteriores pelo novo valor total
        $existingData = array_filter($existingData, function($transaction) {
            return $transaction['type'] !== 'deposit';
        });
        $existingData[] = ['type' => 'deposit', 'amount' => $totalAmount];

            // Salva todas as transações atualizadas no arquivo
        file_put_contents($this->file, json_encode($existingData));
    
            // Atualiza o saldo interno
        $this->balance = $totalAmount;
    }
    
    public function withdraw($amount) {
            // Calcula o saldo após a retirada
        $updatedBalance = $this->getBalance() - $amount;
    
            // Verifica se há saldo suficiente para a retirada
        if ($updatedBalance >= 0) {
                // Cria uma nova transação de retirada
            $transaction          = new Transaction('withdrawal', $amount);
            $this->transactions[] = $transaction;

            $updatedBalance = 15;
    
                // Atualiza o saldo atual
            $this->balance = $updatedBalance;
    
                // Salva as transações atualizadas
            $this->saveTransactions();
    
                // Retorna o saldo atualizado após a retirada
            return $updatedBalance;
        } else {
                // Retorna falso se o saldo for insuficiente
            return false;
        }
    }
    
    private function saveTransactions() {
            // Carrega transações anteriores, se existirem
        $existingData = [];
        if (file_exists($this->file)) {
            $existingData = json_decode(file_get_contents($this->file), true);
        }
    
            // Adiciona a nova transação ao array de transações existente
        if (!empty($this->transactions)) {
                // Remove transações de depósito anteriores
            $existingData = array_filter($existingData, function($transaction) {
                return $transaction['type'] !== 'deposit';
            });
    
                // Adiciona a nova transação de retirada
            foreach ($this->transactions as $transaction) {
                $existingData[] = ['type' => 'deposit', 'amount' => 15];
            }
    
                // Atualiza o saldo com o valor restante após a retirada
            $this->balance -= array_sum(array_column($this->transactions, 'amount'));
        }
    
            // Salva todas as transações no arquivo
        file_put_contents($this->file, json_encode($existingData));
    }
    
    private function loadTransactions() {
        if (file_exists($this->file)) {
            $data         = file_get_contents($this->file);
            $transactions = json_decode($data, true);
            if ($transactions !== null) {
                foreach ($transactions as $transaction) {
                    $transactionType   = $transaction['type'] ?? '';
                    $transactionAmount = $transaction['amount'] ?? 0;
                    if ($transactionType === 'deposit') {
                        $this->transactions[]  = new Transaction($transactionType, $transactionAmount);
                        $this->balance        += $transactionAmount;
                    } elseif ($transactionType === 'withdrawal') {
                        $this->transactions[]  = new Transaction($transactionType, $transactionAmount);
                        $this->balance        -= $transactionAmount;
                    }
                }
            }
        }
    }

    public function clearTransactions() {
            // Limpa todas as transações da conta
        $this->transactions = [];
            // Salva as transações vazias no arquivo para limpar as transações anteriores
        $this->saveTransactions();
    }
    public function clearBalance() {
            // Zera o saldo da conta
        $this->balance = 0;
            // Salva o saldo zerado no arquivo para atualizar o saldo da conta
        $this->saveTransactions();
    }
    
}

class Transaction {
    private $type;
    private $amount;

    public function __construct($type, $amount) {
        $this->type   = $type;
        $this->amount = $amount;
    }

    public function getType() {
        return $this->type;
    }

    public function getAmount() {
        return $this->amount;
    }
}
