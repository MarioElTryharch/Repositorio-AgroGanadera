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
        $sql = "INSERT INTO toros_seguimiento (proximo_control_toro, respon_seguimiento_toro, alimen_especial_toro, supl_vitaminas_toro, costo_mensual_toro, valor_comer_est_toro, plan_rotacion_descanso_toro, recomen_observ_toro)
                VALUES (:proximo_control_toro, :respon_seguimiento_toro, :alimen_especial_toro, :supl_vitaminas_toro, :costo_mensual_toro, :valor_comer_est_toro, :plan_rotacion_descanso_toro, :recomen_observ_toro)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':proximo_control_toro'           => limpiar($_POST['proximo_control_toro'] ?? ''),
            ':respon_seguimiento_toro'        => limpiar($_POST['respon_seguimiento_toro'] ?? ''),
            ':alimen_especial_toro'           => limpiar($_POST['alimen_especial_toro'] ?? ''),
            ':supl_vitaminas_toro'            => limpiar($_POST['supl_vitaminas_toro'] ?? ''),
            ':costo_mensual_toro'             => limpiar($_POST['costo_mensual_toro'] ?? ''),
            ':valor_comer_est_toro'           => limpiar($_POST['valor_comer_est_toro'] ?? ''),
            ':plan_rotacion_descanso_toro'    => limpiar($_POST['plan_rotacion_descanso_toro'] ?? ''),
            ':recomen_observ_toro'            => limpiar($_POST['recomen_observ_toro'] ?? ''),
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
