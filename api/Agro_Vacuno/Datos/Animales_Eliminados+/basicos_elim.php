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
        $sql = "INSERT INTO animales_eliminados (codigo_eliminado, nombre_eliminado, fecha_eliminado, lote_proced_elimi, categoria_elim, raza_elim, edad_meses_elim, peso_kg_elim)
                VALUES (:codigo_eliminado, :nombre_eliminado, :fecha_eliminado, :lote_proced_elimi, :categoria_elim, :raza_elim, :edad_meses_elim, :peso_kg_elim)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_eliminado'      => limpiar($_POST['codigo_animal'] ?? ''),
            ':nombre_eliminado'      => limpiar($_POST['nombre_animal'] ?? ''),
            ':fecha_eliminado'       => limpiar($_POST['fecha_eliminacion'] ?? ''),
            ':lote_proced_elimi'     => limpiar($_POST['lote_proced_elimi'] ?? ''),
            ':categoria_elim'        => limpiar($_POST['categoria_elim'] ?? ''),
            ':raza_elim'             => limpiar($_POST['raza_elim'] ?? ''),
            ':edad_meses_elim'       => limpiar($_POST['edad_meses_elim'] ?? ''),
            ':peso_kg_elim'          => limpiar($_POST['peso_kg_elim'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM animales_eliminados ORDER BY id_elimanado DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
