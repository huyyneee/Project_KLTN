<?php
namespace App\Core;

use PDO, PDOException;

class Database {
    private $conn;
    public function __construct() {
        $config = require(__DIR__ . '/../../config/config.php');
        $dbconf = $config['database'];
        $host = $dbconf['host'];
        $db   = $dbconf['db_name'];
        $user = $dbconf['username'];
        $pass = $dbconf['password'];
        $charset = 'utf8mb4';
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->conn = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
    public function getConnection() {
        return $this->conn;
    }
}