<?php
class Database {
    private $host = "127.0.0.1"; // IP address for localhost
    private $db_name = "todo_list_db"; // Replace with your database name
    private $username = "root"; // MySQL/MariaDB username
    private $password = ""; // MySQL/MariaDB password (use the correct password here)
    private $port = "3307"; // Port set in my.ini
    public $conn;

    // Get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Set the PDO error mode to exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Handle connection error
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
