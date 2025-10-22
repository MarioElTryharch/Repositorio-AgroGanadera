
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
        $sql = "INSERT INTO insemi_rendi (total_insemi, insemi_mes, tasa_preeniez_gen, tasa_preeniez_ult_mes, prom_animales_dia, tiempo_prom_animal_min, eficiencia_tecnica_insemi, calidad_trabajo_insemi, reconoci_logros_insemi, areas_mejora_insemi)
                VALUES (:total_insemi, :insemi_mes, :tasa_preeniez_gen, :tasa_preeniez_ult_mes, :prom_animales_dia, :tiempo_prom_animal_min, :eficiencia_tecnica_insemi, :calidad_trabajo_insemi, :reconoci_logros_insemi, :areas_mejora_insemi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':total_insemi'                   => limpiar($_POST['total_insemi'] ?? ''),
            ':insemi_mes'                     => limpiar($_POST['insemi_mes'] ?? ''),
            ':tasa_preeniez_gen'              => limpiar($_POST['tasa_preeniez_gen'] ?? ''),
            ':tasa_preeniez_ult_mes'          => limpiar($_POST['tasa_preeniez_ult_mes'] ?? ''),
            ':prom_animales_dia'              => limpiar($_POST['prom_animales_dia'] ?? ''),
            ':tiempo_prom_animal_min'         => limpiar($_POST['tiempo_prom_animal_min'] ?? ''),
            ':eficiencia_tecnica_insemi'      => limpiar($_POST['eficiencia_tecnica_insemi'] ?? ''),
            ':calidad_trabajo_insemi'         => limpiar($_POST['calidad_trabajo_insemi'] ?? ''),
            ':reconoci_logros_insemi'         => limpiar($_POST['reconoci_logros_insemi'] ?? ''),
            ':areas_mejora_insemi'            => limpiar($_POST['areas_mejora_insemi'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM insemi_rendi ORDER BY id_inseminador DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
