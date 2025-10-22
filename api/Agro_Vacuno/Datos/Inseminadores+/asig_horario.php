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
        $sql = "INSERT INTO insemi_asignaciones (estado_laboral_insemi, tipo_contrato_insemi, horario_trabajo_insemi, dias_descanso_insemi, lotes_fincas_asignados, equipos_asignados_insemi, salario_base_insemi, bonos_rendi_insemi, seguro_medico_insemi, proxima_evaluacion_insemi, obser_gener_insemi)
                VALUES (:estado_laboral_insemi, :tipo_contrato_insemi, :horario_trabajo_insemi, :dias_descanso_insemi, :lotes_fincas_asignados, :equipos_asignados_insemi, :salario_base_insemi, :bonos_rendi_insemi, :seguro_medico_insemi, :proxima_evaluacion_insemi, :obser_gener_insemi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado_laboral_insemi'             => limpiar($_POST['estado_laboral_insemi'] ?? ''),
            ':tipo_contrato_insemi'              => limpiar($_POST['tipo_contrato_insemi'] ?? ''),
            ':horario_trabajo_insemi'            => limpiar($_POST['horario_trabajo_insemi'] ?? ''),
            ':dias_descanso_insemi'              => limpiar($_POST['dias_descanso_insemi'] ?? ''),
            ':lotes_fincas_asignados'            => limpiar($_POST['lotes_fincas_asignados'] ?? ''),
            ':equipos_asignados_insemi'          => limpiar($_POST['equipos_asignados_insemi'] ?? ''),
            ':salario_base_insemi'               => limpiar($_POST['salario_base_insemi'] ?? ''),
            ':bonos_rendi_insemi'                => limpiar($_POST['bonos_rendi_insemi'] ?? ''),
            ':seguro_medico_insemi'              => limpiar($_POST['seguro_medico_insemi'] ?? ''),
            ':proxima_evaluacion_insemi'         => limpiar($_POST['proxima_evaluacion_insemi'] ?? ''),
            ':obser_gener_insemi'                => limpiar($_POST['obser_gener_insemi'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM insemi_asignaciones ORDER BY id_inseminador DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
