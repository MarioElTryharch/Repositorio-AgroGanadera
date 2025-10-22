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
        $sql = "INSERT INTO desarrollo_peso_cria (peso_actual_cria, fecha_ult_pesaje_cria, ganancia_total_cria, ganancia_dia_destete_cria, edad_destete_cria, peso_destete_cria, condicion_corporal_cria, desarrollo_muscular_cria, altura_cruz_cria, circu_torax_cria)
                VALUES (:peso_actual_cria, :fecha_ult_pesaje_cria, :ganancia_total_cria, :ganancia_dia_destete_cria, :edad_destete_cria, :peso_destete_cria, :condicion_corporal_cria, :desarrollo_muscular_cria, :altura_cruz_cria, :circu_torax_cria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':peso_actual_cria'                  => limpiar($_POST['peso_actual_cria'] ?? ''),
            ':fecha_ult_pesaje_cria'             => limpiar($_POST['fecha_ult_pesaje_cria'] ?? ''),
            ':ganancia_total_cria'               => limpiar($_POST['ganancia_total_cria'] ?? ''),
            ':ganancia_dia_destete_cria'         => limpiar($_POST['ganancia_dia_destete_cria'] ?? ''),
            ':edad_destete_cria'                 => limpiar($_POST['edad_destete_cria'] ?? ''),
            ':peso_destete_cria'                 => limpiar($_POST['peso_destete_cria'] ?? ''),
            ':condicion_corporal_cria'           => limpiar($_POST['condicion_corporal_cria'] ?? ''),
            ':desarrollo_muscular_cria'          => limpiar($_POST['desarrollo_muscular_cria'] ?? ''),
            ':altura_cruz_cria'                  => limpiar($_POST['altura_cruz_cria'] ?? ''),
            ':circu_torax_cria'                  => limpiar($_POST['circu_torax_cria'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Cria agregado correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM desarrollo_peso_cria ORDER BY id_cria DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
