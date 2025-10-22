
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
        $sql = "INSERT INTO insemi_datos_personales (codigo_id_insemi, nombre_insemi, tipo_docum_insemi, numero_docum_insemi, fecha_nac_insemi, edad_insemi, teléfono_insemi, correo_insemi, direccion_insemi, ciudad_local_insemi, estado_civil_insemi, fecha_ingr_insemi)
                VALUES (:codigo_id_insemi, :nombre_insemi, :tipo_docum_insemi, :numero_docum_insemi, :fecha_nac_insemi, :edad_insemi, :teléfono_insemi, :correo_insemi, :direccion_insemi, :ciudad_local_insemi, :estado_civil_insemi, :fecha_ingr_insemi)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_id_insemi'              => limpiar($_POST['codigo_id_insemi'] ?? ''),
            ':nombre_insemi'                 => limpiar($_POST['nombre_insemi'] ?? ''),
            ':tipo_docum_insemi'             => limpiar($_POST['tipo_documento'] ?? ''),
            ':numero_docum_insemi'           => limpiar($_POST['numero_docum_insemi'] ?? ''),
            ':fecha_nac_insemi'              => limpiar($_POST['fecha_nac_insemi'] ?? ''),
            ':edad_insemi'                   => limpiar($_POST['edad_insemi'] ?? ''),
            ':teléfono_insemi'               => limpiar($_POST['teléfono_insemi'] ?? ''),
            ':correo_insemi'                 => limpiar($_POST['correo_insemi'] ?? ''),
            ':direccion_insemi'              => limpiar($_POST['direccion_insemi'] ?? ''),
            ':ciudad_local_insemi'           => limpiar($_POST['ciudad_local_insemi'] ?? ''),
            ':estado_civil_insemi'           => limpiar($_POST['estado_civil_insemi'] ?? ''),
            ':fecha_ingr_insemi'             => limpiar($_POST['fecha_ingr_insemi'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM insemi_datos_personales ORDER BY id_inseminador DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
