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
        /*Dias parida, ultimo pesaje (kg), Dias secado, ultimo secado (date), ultima parto (date), Numero de partos, Promedio de Produccion (L/dia), Proximo secado (date), Dias seca*/
        $sql = "INSERT INTO datos_toros (codigo_id_toro, nombre_toro, raza_toro, procedencia_toro, fecha_nac_toro, edad_toro, peso_actual_toro, condi_corp_toro, lote_asig_toro, corral_toro)
                VALUES (:codigo_id_toro, :nombre_toro, :raza_toro, :procedencia_toro, :fecha_nac_toro, :edad_toro, :peso_actual_toro, :condi_corp_toro, :lote_asig_toro, :corral_toro)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_id_toro'           => limpiar($_POST['codigo_id_toro'] ?? ''),
            ':nombre_toro'              => limpiar($_POST['nombre_toro'] ?? ''),
            ':raza_toro'                => limpiar($_POST['raza_toro'] ?? ''),
            ':procedencia_toro'         => limpiar($_POST['procedencia_toro'] ?? ''),
            ':fecha_nac_toro'           => limpiar($_POST['fecha_nac_toro'] ?? ''),
            ':edad_toro'                => limpiar($_POST['edad_toro'] ?? ''),
            ':peso_actual_toro'         => limpiar($_POST['peso_actual_toro'] ?? ''),
            ':condi_corp_toro'          => limpiar($_POST['condi_corp_toro'] ?? ''),
            ':lote_asig_toro'           => limpiar($_POST['lote_asig_toro'] ?? ''),
            ':corral_toro'              => limpiar($_POST['corral_toro'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Toro agregado correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM datos_toros ORDER BY id_toro DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
