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
        $sql = "INSERT INTO vientres_alimentacion_manejo (sist_alim, tipo_dieta, consu_diario, suple_utili, manejo_reque, adap_manejo)
                VALUES (:sist_alim, :tipo_dieta, :consu_diario, :suple_utili, :manejo_reque, :adap_manejo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':sist_alim'        => limpiar($_POST['sist_alim'] ?? ''),
            ':tipo_dieta'       => limpiar($_POST['tipo_dieta'] ?? 0),
            ':consu_diario'     => limpiar($_POST['consu_diario'] ?? ''),
            ':suple_utili'      => limpiar($_POST['suple_utili'] ?? ''),
            ':manejo_reque'     => limpiar($_POST['manejo_reque'] ?? ''),
            ':adap_manejo'      => limpiar($_POST['adap_manejo'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM vientres_alimentacion_manejo ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
