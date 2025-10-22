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
        $sql = "INSERT INTO datos_basicos_cria (tipo_categoria, raza_principal, composicion_racial, color_cria, codigo_madre_cria, codigo_padre_cria, lote_origen_cria, corral_actual_cria, grupo_manejo_cria, estado_desarrollo_cria)
                VALUES (:tipo_categoria, :raza_principal, :composicion_racial, :color_cria, :codigo_madre_cria, :codigo_padre_cria, :lote_origen_cria, :corral_actual_cria, :grupo_manejo_cria, :estado_desarrollo_cria)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':tipo_categoria'               => limpiar($_POST['tipo_categoria'] ?? ''),
            ':raza_principal'               => limpiar($_POST['raza_principal'] ?? ''),
            ':composicion_racial'           => limpiar($_POST['composicion_racial'] ?? ''),
            ':color_cria'                   => limpiar($_POST['color_cria'] ?? ''),
            ':codigo_madre_cria'            => limpiar($_POST['codigo_madre_cria'] ?? ''),
            ':codigo_padre_cria'            => limpiar($_POST['codigo_padre_cria'] ?? ''),
            ':lote_origen_cria'             => limpiar($_POST['lote_origen_cria'] ?? ''),
            ':corral_actual_cria'           => limpiar($_POST['corral_actual_cria'] ?? ''),
            ':grupo_manejo_cria'            => limpiar($_POST['grupo_manejo_cria'] ?? ''),
            ':estado_desarrollo_cria'       => limpiar($_POST['estado_desarrollo_cria'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Cria agregada correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM datos_basicos_cria ORDER BY id_cria DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
