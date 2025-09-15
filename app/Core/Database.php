<?php
class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;
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
    public function getConnection()
    {
        // Use singleton pattern to reuse connection
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    // Performance optimizations
                    PDO::ATTR_PERSISTENT => true, // Enable persistent connections
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                error_log('Database connection error: ' . $e->getMessage());
                throw new Exception('Database connection failed');
            }
        }
        return $this->conn;
    }
}
