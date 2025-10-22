<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agrobufalino22";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'];
    
    $sql = "DELETE FROM vientres_basicos WHERE id_vientres = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vientre eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar vientre']);
    }
}

$conn->close();
?>