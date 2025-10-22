s<?php
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
        $sql = "INSERT INTO aspectos_economicos (valor_estimado_elim, precio_venta_real_elim, destino_final_elim, costos_asociados_elim, descripcion_costos_elim, perdida_estimada_elim, cubierto_seguro_elim)
                VALUES (:valor_estimado_elim, :precio_venta_real_elim, :destino_final_elim, :costos_asociados_elim, :descripcion_costos_elim, :perdida_estimada_elim, :cubierto_seguro_elim)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':valor_estimado_elim'           => limpiar($_POST['valor_estimado_elim'] ?? ''),
            ':precio_venta_real_elim'        => limpiar($_POST['precio_venta_real_elim'] ?? ''),
            ':destino_final_elim'            => limpiar($_POST['destino_final_elim'] ?? ''),
            ':costos_asociados_elim'         => limpiar($_POST['costos_asociados_elim'] ?? ''),
            ':descripcion_costos_elim'       => limpiar($_POST['descripcion_costos_elim'] ?? ''),
            ':perdida_estimada_elim'         => limpiar($_POST['perdida_estimada_elim'] ?? ''),
            ':cubierto_seguro_elim'          => limpiar($_POST['cubierto_seguro_elim'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Animal Eliminado agregado correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM aspectos_economicos_elimi ORDER BY id_elimanado DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
