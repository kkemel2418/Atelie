<?php

class Database {
    private $host = "localhost"; // Endereço do servidor MySQL
    private $db_name = "AtelieCampanhas"; // Nome do seu banco de dados
    private $username = "root"; // Nome de usuário do MySQL
    private $password = ""; // Senha do MySQL
    public $conn; // Objeto de conexão
    // Método de conexão
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
