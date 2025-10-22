<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../../../../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM vientres_basicos");
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(["total" => $data['total']]);
} catch (PDOException $e) {
    echo json_encode(["total" => 0]);
}
?>
