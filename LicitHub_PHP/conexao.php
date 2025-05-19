<?php
// conexao.php

class Conexao {
    private static $instancia;
    private $pdo;
    
    // Configurações do banco de dados
    private $host = 'localhost';
    private $dbname = 'licithub';
    private $usuario = 'root';
    private $senha = '';
    private $charset = 'utf8mb4';

    // Impedir instanciação direta
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->usuario, $this->senha);
            
            // Configurar PDO para lançar exceções em caso de erros
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Garantir que os dados retornados sejam no formato de array associativo
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Desativar emulação de prepared statements para segurança
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            // Em produção, você pode querer registrar este erro em um log
            throw new Exception("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }

    // Método para obter a instância única
    public static function getInstancia() {
        if (!isset(self::$instancia)) {
            self::$instancia = new Conexao();
        }
        return self::$instancia;
    }

    // Método para obter a conexão PDO diretamente
    public function getPdo() {
        return $this->pdo;
    }

    // Impedir clonagem
    private function __clone() { }

    // Impedir desserialização
    public function __wakeup() {
        throw new Exception("Não é possível desserializar uma conexão com o banco de dados");
    }
}

// Função auxiliar para obter a conexão PDO
function getConexao() {
    return Conexao::getInstancia()->getPdo();
}