<?php
// Mostrar errores (solo durante desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ==========================================
// 🔹 CONEXIÓN A LA BASE DE DATOS
// ==========================================
require_once __DIR__ . '/../../../../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die(json_encode(["error" => "No se pudo conectar a la base de datos."]));
}

// ==========================================
// 🔹 LIMPIAR DATOS
// ==========================================
function limpiar($data) {
    return htmlspecialchars(trim($data));
}

// ==========================================
// 🔸 INSERTAR NUEVO REGISTRO
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = "INSERT INTO documentacion_cria (obser_gener, potencial_reproductivo, destino_final, imagen_guardadas_cria)
                VALUES (:obser_gener, :potencial_reproductivo, :destino_final, :imagen_guardadas_cria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':obser_gener_cria'               => limpiar($_POST['obser_gener_cria'] ?? ''),
            ':potencial_reproduc_cria'        => limpiar($_POST['potencial_reproduc_cria'] ?? 0),
            ':destino_final_cria'             => limpiar($_POST['destino_final_cria'] ?? ''),
            ':imagen_guardadas_cria'          => limpiar($_POST['imagen_guardadas_cria'] ?? 0),
        ]);

        

        echo json_encode(["success" => true, "message" => "✅ Vientres agregado correctamente."]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "❌ Error al guardar: " . $e->getMessage()]);
        exit;
    }
}

// ==========================================
// 🔹 OBTENER TODOS LOS REGISTROS (GET)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT * FROM documentacion_cria ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
