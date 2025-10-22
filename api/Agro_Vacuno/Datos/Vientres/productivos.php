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
        $sql = "INSERT INTO vientres_productivos (estado, dias_parida, ultimo_pesaje, dias_secado, ultimo_secado, ultima_parto, numero_partos, promedio_produccion, proximo_secado, dias_seca, produccion_max, calidad_leche, obser_produc)
                VALUES (:estado, :dias_parida, :ultimo_pesaje, :dias_secado, :ultimo_secado, :ultima_parto, :numero_partos, :promedio_produccion, :proximo_secado, :dias_seca, :produccion_max, :calidad_leche, :obser_produc)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':estado_prod'           => limpiar($_POST['estado_prod'] ?? ''),
            ':dias_parida'           => limpiar($_POST['dias_parida'] ?? 0),
            ':ultimo_pesaje'         => limpiar($_POST['ultimo_pesaje'] ?? 0),
            ':dias_secado'           => limpiar($_POST['dias_secado'] ?? 0),
            ':ultimo_secado'         => limpiar($_POST['ultimo_secado'] ?? ''),
            ':ultima_parto'          => limpiar($_POST['ultima_parto'] ?? ''),
            ':numero_partos'         => limpiar($_POST['numero_partos'] ?? 0),
            ':promedio_produccion'   => limpiar($_POST['promedio_produccion'] ?? 0),
            ':proximo_secado'        => limpiar($_POST['proximo_secado'] ?? ''),
            ':dias_seca'             => limpiar($_POST['dias_seca'] ?? 0),
            ':produccion_max'        => limpiar($_POST['produccion_max'] ?? 0),
            ':calidad_leche'         => limpiar($_POST['calidad_leche'] ?? ''),
            ':obser_produc'          => limpiar($_POST['obser_produc'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM vientres_productivos ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
