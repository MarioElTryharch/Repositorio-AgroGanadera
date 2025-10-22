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
        $sql = "INSERT INTO vientres_genealogia (codigo_padre, abuelo_paterno, abuela_paterna, codigo_madre, abuelo_materno, abuela_materna, linea_genetica, valor_genetica, ante_geneticos)
                VALUES (:codigo_padre, :abuelo_paterno, :abuela_paterna, :codigo_madre, :abuelo_materno, :abuela_materna, :linea_genetica, :valor_genetica, :ante_geneticos)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo_padre'      => limpiar($_POST['codigo_padre'] ?? ''),
            ':abuelo_paterno'    => limpiar($_POST['abuelo_paterno'] ?? ''),
            ':abuela_paterna'    => limpiar($_POST['abuela_paterna'] ?? ''),
            ':codigo_madre'      => limpiar($_POST['codigo_madre'] ?? ''),
            ':abuelo_materno'    => limpiar($_POST['abuelo_materno'] ?? ''),
            ':abuela_materna'    => limpiar($_POST['abuela_materna'] ?? ''),
            ':linea_genetica'    => limpiar($_POST['linea_genetica'] ?? ''),
            ':valor_genetica'    => limpiar($_POST['valor_genetica'] ?? ''),
            ':ante_geneticos'    => limpiar($_POST['ante_geneticos'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM vientres_genealogia ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
