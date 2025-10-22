<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agrobufalino22";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta para obtener los vientres
    $sql = "SELECT 
                id_vientre,
                nombre_animal,
                tipo_animal,
                codigo_id_animal,
                fecha_nac,
                cruce_animal,
                corral_animal,
                grupo_animal,
                edad_meses,
                raza_princ,
                color_animal,
                lote_animal,
                peso_act,
                condi_corp,
                fecha_ingr,
                origen_animal,
                'Activo' as estado
            FROM vientres_basicos 
            ORDER BY id_vientre DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $vientres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'data' => $vientres
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al cargar los vientres: ' . $e->getMessage()
    ]);
}

$conn = null;
?>