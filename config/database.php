<?php
/**
 * FitHub Gym Management System
 * Database Connection Configuration
 * 
 * This file handles PDO database connection with error handling
 * and secure configuration management
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

class Database {
    // Database credentials - These should be in .env file in production
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset = 'utf8mb4';
    
    public $conn;
    
    /**
     * Constructor - Initialize database credentials based on environment
     */
    public function __construct() {
        // Load environment-specific configuration
        $this->loadEnvironmentConfig();
    }
    
    /**
     * Load configuration based on environment
     */
    private function loadEnvironmentConfig() {
        // Check if running in production or development
        $environment = getenv('APP_ENV') ?: 'development';
        
        if ($environment === 'production') {
            // Production credentials (should be loaded from .env file)
            $this->host = getenv('DB_HOST') ?: 'localhost';
            $this->db_name = getenv('DB_NAME') ?: 'fithub_gym';
            $this->username = getenv('DB_USER') ?: 'root';
            $this->password = getenv('DB_PASS') ?: '';
        } else {
            // Development credentials
            $this->host = 'localhost';
            $this->db_name = 'fithub_gym';
            $this->username = 'root';
            $this->password = '';
        }
    }
    
    /**
     * Create database connection using PDO
     * @return PDO|null Database connection object or null on failure
     */
    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            // Set timezone to match PHP timezone
            $this->conn->exec("SET time_zone = '+00:00'");
            
        } catch(PDOException $exception) {
            // Log error in production, display in development
            $environment = getenv('APP_ENV') ?: 'development';
            
            if ($environment === 'production') {
                // Log to error file
                error_log("Database Connection Error: " . $exception->getMessage());
                die("Database connection failed. Please contact administrator.");
            } else {
                // Display error for debugging
                echo "Database Connection Error: " . $exception->getMessage();
                die();
            }
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
    
    /**
     * Test database connection
     * @return bool True if connection successful, false otherwise
     */
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn !== null) {
                $this->closeConnection();
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Execute a query and return results
     * @param string $query SQL query to execute
     * @param array $params Parameters for prepared statement
     * @return array|false Query results or false on failure
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Execute an insert/update/delete query
     * @param string $query SQL query to execute
     * @param array $params Parameters for prepared statement
     * @return bool True on success, false on failure
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Execute Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get last inserted ID
     * @return string Last insert ID
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        return $this->conn->rollBack();
    }
}

// Create global database instance
$database = new Database();
$db = $database->getConnection();

// Test connection on initialization
if ($db === null) {
    die("Failed to establish database connection");
}
?>
