<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Cambiar por tus credenciales
$password = ""; // Cambiar por tus credenciales
$dbname = "agrobufalino22";

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);
$codigo_animal = $input['codigo_animal'] ?? '';
$tipo_animal = $input['tipo_animal'] ?? 'vientre';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$response = ['success' => false, 'message' => ''];

try {
    if ($tipo_animal === 'vientre') {
        // Actualizar vientre
        $sql = "UPDATE vientres SET 
                ult_desparas = NOW(),
                prox_desparas = DATE_ADD(NOW(), INTERVAL 90 DAY),
                vacuna_dia = 'completo'
                WHERE codigo_id_animal = ?";
    } else {
        // Actualizar animal de cría
        $sql = "UPDATE animales_cria SET 
                ultima_desparasitacion_cria = NOW(),
                proxima_desparasitacion_cria = DATE_ADD(NOW(), INTERVAL 90 DAY),
                vacunaciones_cria = 'completo'
                WHERE codigo_identificacion_cria = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $codigo_animal);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = ['success' => true, 'message' => 'Animal marcado como vacunado correctamente'];
        } else {
            $response = ['success' => false, 'message' => 'No se encontró el animal especificado'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Error al actualizar: ' . $stmt->error];
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

$conn->close();
echo json_encode($response);
?>