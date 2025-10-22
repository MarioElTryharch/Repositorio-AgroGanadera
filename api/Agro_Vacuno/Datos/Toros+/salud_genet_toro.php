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
        $sql = "INSERT INTO toros_salud_genetica (estado_salud_toro, ult_revi_veteri_toro, vacuna_al_dia_toro, ultima_desparasita_toro, evalu_fertilidad_toro, calidad_semen_toro, enferm_detectadas_toro, tratamiento_curso_toro, antece_geneticos_toro)
                VALUES (:estado_salud_toro, :ult_revi_veteri_toro, :vacuna_al_dia_toro, :ultima_desparasita_toro, :evalu_fertilidad_toro, :calidad_semen_toro, :enferm_detectadas_toro, :antece_geneticos_toro)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado_salud_toro'            => limpiar($_POST['estado_salud_toro'] ?? ''),
            ':ult_revi_veteri_toro'         => limpiar($_POST['ult_revi_veteri_toro'] ?? ''),
            ':vacuna_al_dia_toro'           => limpiar($_POST['vacuna_al_dia_toro'] ?? ''),
            ':ultima_desparasita_toro'      => limpiar($_POST['ultima_desparasita_toro'] ?? ''),
            ':evalu_fertilidad_toro'        => limpiar($_POST['evalu_fertilidad_toro'] ?? ''),
            ':calidad_semen_toro'           => limpiar($_POST['calidad_semen_toro'] ?? ''),
            ':enferm_detectadas_toro'       => limpiar($_POST['enferm_detectadas_toro'] ?? ''),
            ':tratamiento_curso_toro'       => limpiar($_POST['tratamiento_curso_toro'] ?? ''),
            ':antece_geneticos_toro'        => limpiar($_POST['antece_geneticos_toro'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM toros_salud_genetica ORDER BY id_toro DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
