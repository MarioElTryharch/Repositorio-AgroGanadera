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
        $sql = "INSERT INTO manejo_alimentacion_cria (sistema_alimen_cria, tipo_dieta_cria, consumo_diario_kg_cria, conversion_aliment_cria, suple_utilizados_cria, manejo_especial_cria, fecha_destete_cria, fecha_descorne_cria, fecha_id_cria, metodo_identificacion_cria)
                VALUES (:sistema_alimen_cria, :tipo_dieta_cria, :consumo_diario_kg_cria, :conversion_aliment_cria, :suple_utilizados_cria, :manejo_especial_cria, :fecha_destete_cria, :fecha_descorne_cria, :fecha_id_cria, :metodo_identificacion_cria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':sistema_alimen_cria'           => limpiar($_POST['sistema_alimen_cria'] ?? ''),
            ':tipo_dieta_cria'               => limpiar($_POST['tipo_dieta_cria'] ?? ''),
            ':consumo_diario_kg_cria'        => limpiar($_POST['consumo_diario_kg_cria'] ?? ''),
            ':conversion_aliment_cria'       => limpiar($_POST['conversion_aliment_cria'] ?? ''),
            ':suple_utilizados_cria'         => limpiar($_POST['suple_utilizados_cria'] ?? ''),
            ':manejo_especial_cria'          => limpiar($_POST['manejo_especial_cria'] ?? ''),
            ':fecha_destete_cria'            => limpiar($_POST['fecha_destete_cria'] ?? ''),
            ':fecha_descorne_cria'           => limpiar($_POST['fecha_descorne_cria'] ?? ''),
            ':fecha_id_cria'                 => limpiar($_POST['fecha_id_cria'] ?? ''),
            ':metodo_identificacion_cria'    => limpiar($_POST['metodo_identificacion_cria'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM manejo_alimentacion_cria ORDER BY id_cria DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
