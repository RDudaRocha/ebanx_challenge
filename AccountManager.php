<?php

    // AccountManager.php

class AccountManager {
    private static $accounts      = [];
    private static $file          = 'accounts.txt';
    private static $fileDirectory = '';

    public static function resetAccounts() {
            // Limpa os dados em memória
        self::$accounts = [];

            // Exclui os arquivos de contas
        $files = glob(self::$fileDirectory . '*.txt');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    public static function accountExists($accountId) {
              // Verifica se o arquivo da conta existe
        return file_exists($accountId . '.txt');
    }

    public static function createAccount($id, $initialBalance = 0) {
        if (!isset(self::$accounts[$id])) {
            self::$accounts[$id] = new Account($id);
            self::$accounts[$id]->setInitialBalance($initialBalance);  // Define o saldo inicial
            self::saveAccounts();
        }
    }
    
    public static function updateAccountFile($accountId, $newBalance) {
        $filename    = $accountId . '.txt';
        $fileContent = json_encode(['balance' => $newBalance]);

            // Escreve o novo saldo no arquivo correspondente à conta
        file_put_contents($filename, $fileContent);
    }

    public static function getAccount($id) {
        // Carregar contas se ainda não estiverem carregadas
    self::loadAccounts();
    
        // Verifica se o arquivo da conta existe
    if (file_exists($id . '.txt')) {
        return isset(self::$accounts[$id]) ? self::$accounts[$id]: null;
    } else {
        return null;
    }
}
    
    private static function saveAccounts() {
        file_put_contents(self::$file, serialize(self::$accounts));
    }

    private static function loadAccounts() {
        if (file_exists(self::$file)) {
            self::$accounts = unserialize(file_get_contents(self::$file));
        }
    }
}
