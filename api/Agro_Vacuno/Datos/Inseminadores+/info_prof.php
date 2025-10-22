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
        $sql = "INSERT INTO insemi_info_prof (nivel_estudios_insemi, titu_certi_insemi, inst_form_insemi, anio_grad_insemi, experiencia_total_anios, exp_inseminacion_anios, numero_certif_insemi, fecha_ult_certif_insemi, especializa_insemi, habili_tecnicas_insemi)
                VALUES (:nivel_estudios_insemi, :titu_certi_insemi, :inst_form_insemi, :anio_grad_insemi, :experiencia_total_anios, :exp_inseminacion_anios, :numero_certif_insemi, :fecha_ult_certif_insemi, :especializa_insemi, :habili_tecnicas_insemi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nivel_estudios_insemi'                => limpiar($_POST['nivel_estudios_insemi'] ?? ''),
            ':titu_certi_insemi'                    => limpiar($_POST['titu_certi_insemi'] ?? ''),
            ':inst_form_insemi'                     => limpiar($_POST['inst_form_insemi'] ?? ''),
            ':anio_grad_insemi'                     => limpiar($_POST['anio_grad_insemi'] ?? ''),
            ':experiencia_total_anios'              => limpiar($_POST['experiencia_total_anios'] ?? ''),
            ':exp_inseminacion_anios'               => limpiar($_POST['exp_inseminacion_anios'] ?? ''),
            ':numero_certif_insemi'                 => limpiar($_POST['numero_certif_insemi'] ?? ''),
            ':fecha_ult_certif_insemi'              => limpiar($_POST['fecha_ult_certif_insemi'] ?? ''),
            ':especializa_insemi'                   => limpiar($_POST['especializa_insemi'] ?? ''),
            ':habili_tecnicas_insemi'               => limpiar($_POST['habili_tecnicas_insemi'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Inseminador agregado correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM insemi_info_prof ORDER BY id_inseminador DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
