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
        $sql = "INSERT INTO vientres_salud_sanidad (estado_salud, vacuna_dia, ult_desparas, prox_desparas, enferm_cronicas, tratamiento_curso, problemas_partos, ultima_revi, obser_sanitaria)
                VALUES (:estado_salud, :vacuna_dia, :ult_desparas, :prox_desparas, :enferm_cronicas, :tratamiento_curso, :problemas_partos, :ultima_revi, :obser_sanitaria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado_salud'        => limpiar($_POST['estado_salud'] ?? ''),
            ':vacuna_dia'          => limpiar($_POST['vacuna_dia'] ?? 0),
            ':ult_desparas'        => limpiar($_POST['ult_desparas'] ?? ''),
            ':prox_desparas'       => limpiar($_POST['prox_desparas'] ?? ''),
            ':enferm_cronicas'     => limpiar($_POST['enferm_cronicas'] ?? ''),
            ':tratamiento_curso'   => limpiar($_POST['tratamiento_curso'] ?? ''),
            ':problemas_partos'    => limpiar($_POST['problemas_partos'] ?? ''),
            ':ultima_revi'         => limpiar($_POST['ultima_revi'] ?? ''),
            ':obser_sanitaria'     => limpiar($_POST['obser_sanitaria'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM vientres_salud_sanidad ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
