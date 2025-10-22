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
        $sql = "INSERT INTO salud_sanidad_cria (estado_salud_cria, vacuna_dia_cria, ult_desparas_cria, prox_desparas_cria, enferm_detect_cria, tratamiento_curso_cria, consumo_calostro_cria, inmuni_transf_cria, obser_salud_cria)
                VALUES (:estado_salud_cria, :vacuna_dia_cria, :ult_desparas_cria, :prox_desparas_cria, :enferm_detect_cria, :tratamiento_curso_cria, :consumo_calostro_cria, :inmuni_transf_cria, :obser_salud_cria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado_salud_cria'          => limpiar($_POST['estado_salud_cria'] ?? ''),
            ':vacuna_dia_cria'            => limpiar($_POST['vacuna_dia_cria'] ?? ''),
            ':ult_desparas_cria'          => limpiar($_POST['ult_desparas_cria'] ?? ''),
            ':prox_desparas_cria'         => limpiar($_POST['prox_desparas_cria'] ?? ''),
            ':enferm_detect_cria'         => limpiar($_POST['enferm_detect_cria'] ?? ''),
            ':tratamiento_curso_cria'     => limpiar($_POST['tratamiento_curso_cria'] ?? ''),
            ':consumo_calostro_cria'      => limpiar($_POST['consumo_calostro_cria'] ?? ''),
            ':inmuni_transf_cria'         => limpiar($_POST['inmuni_transf_cria'] ?? ''),
            ':obser_salud_cria'           => limpiar($_POST['obser_salud_cria'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM salud_sanidad_cria ORDER BY id_cria DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
