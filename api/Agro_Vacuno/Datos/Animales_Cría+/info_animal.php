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
        $sql = "INSERT INTO cria_informacion_animal (codigo_id_cria, nombre_cria, fecha_nac_cria, hora_nac_cria, tipo_parto, peso_nacer)
                VALUES (:codigo_id_cria, :nombre_cria, :fecha_nac_cria, :hora_nac_cria, :tipo_parto, :peso_nacer)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_id_cria'   => limpiar($_POST['codigo_id_cria'] ?? ''),
            ':nombre_cria'      => limpiar($_POST['nombre_cria'] ?? ''),
            ':fecha_nac_cria'   => limpiar($_POST['fecha_nac_cria'] ?? ''),
            ':hora_nac_cria'    => limpiar($_POST['hora_nac_cria'] ?? ''),
            ':tipo_parto_cria'       => limpiar($_POST['tipo_parto'] ?? ''),
            ':peso_nacer_cria'       => limpiar($_POST['peso_nacer'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM cria_informacion_animal ORDER BY id_cria DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
