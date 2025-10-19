<?php
namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
    private static $instance = null;
    public function __construct()
    {
        $configFile = __DIR__ . '/../../config/config.php';
        if (file_exists($configFile)) {
            $config = include $configFile;
            if (is_array($config) && isset($config['database'])) {
                $this->host = $config['database']['host'];
                $this->db_name = $config['database']['db_name'];
                $this->username = $config['database']['username'];
                $this->password = $config['database']['password'];
            } else {
                throw new Exception('Invalid database configuration');
            }
        } else {
            throw new Exception('Database configuration file not found');
        }
    }
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        // Use singleton pattern to reuse connection
        if ($this->conn === null) {
            try {
                // Set execution time limit for database operations
                set_time_limit(60);
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci; SET SESSION wait_timeout=28800, interactive_timeout=28800",
                        // Connection timeout settings
                    PDO::ATTR_TIMEOUT => 10,
                        // Disable persistent connections to avoid hanging
                    PDO::ATTR_PERSISTENT => false,
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
                error_log('Database connection established successfully');
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                error_log('DSN: ' . $dsn);
                error_log('Host: ' . $this->host . ', DB: ' . $this->db_name);
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        return $this->conn;
    }
}