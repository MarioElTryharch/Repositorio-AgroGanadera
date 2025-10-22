<?php
class Database {
    private $host = "localhost";
    private $db_name = "agrobufalino22";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "✅ Conexión establecida correctamente a MySQL.";
        } catch (PDOException $e) {
            die("❌ Error de conexión a MySQL: " . $e->getMessage());
        }

        return $this->conn;
    }
}
?>
