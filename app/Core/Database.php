<?php
class Database {
    private $host; private $db_name; private $username; private $password; private $conn;
    public function __construct() {
        $config = include_once __DIR__ . '/../../config/config.php';
        $this->host = $config['database']['host'];
        $this->db_name = $config['database']['db_name'];
        $this->username = $config['database']['username'];
        $this->password = $config['database']['password'];
    }
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec('set names utf8');
        } catch (PDOException $e) { echo 'Connection error: ' . $e->getMessage(); }
        return $this->conn;
    }
}
