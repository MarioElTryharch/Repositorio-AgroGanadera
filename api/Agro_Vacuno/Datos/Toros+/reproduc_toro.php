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
        $sql = "INSERT INTO toros_reproductivos (fecha_inicio_serv_toro, tipo_servicio_toro, numero_vientres_asig_toro, ratio_monta_toro, servicios_reali_toro, preniez_confir_toro, tasa_preniez_toro, eficiencia_reproduc_toro, observ_reproduc_toro)
                VALUES (:fecha_inicio_serv_toro, :tipo_servicio_toro, :numero_vientres_asig_toro, :ratio_monta_toro, :servicios_reali_toro, :preniez_confir_toro, :tasa_preniez_toro, :eficiencia_reproduc_toro, :observ_reproduc_toro)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':fecha_inicio_serv_toro'             => limpiar($_POST['fecha_inicio_serv_toro'] ?? ''),
            ':tipo_servicio_toro'                 => limpiar($_POST['tipo_servicio_toro'] ?? ''),
            ':numero_vientres_asig_toro'          => limpiar($_POST['numero_vientres_asig_toro'] ?? ''),
            ':ratio_monta_toro'                   => limpiar($_POST['ratio_monta_toro'] ?? ''),
            ':servicios_reali_toro'               => limpiar($_POST['servicios_reali_toro'] ?? ''),
            ':preniez_confir_toro'                => limpiar($_POST['preniez_confir_toro'] ?? ''),
            ':tasa_preniez_toro'                  => limpiar($_POST['tasa_preniez_toro'] ?? ''),
            ':eficiencia_reproduc_toro'           => limpiar($_POST['eficiencia_reproduc_toro'] ?? ''),
            ':observ_reproduc_toro'               => limpiar($_POST['observ_reproduc_toro'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM toros_reproductivos ORDER BY id_toro DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
