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
        $sql = "INSERT INTO vientres_reproductivos (condicion_reprod, dias_servida, resultado_serv, proximo_parto, ultimo_servicio, ultima_palpitacion, tipo_servicio, toro_sem_utilizado, num_ciclos_obs, regul_celos, diagnostico)
                VALUES (:condicion_reprod, :dias_servida, :resultado_serv, :proximo_parto, :ultimo_servicio, :ultima_palpitacion, :tipo_servicio, :toro_sem_utilizado, :num_ciclos_obs, :regul_celos, :diagnostico)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':condicion_reprod'     => limpiar($_POST['condicion_reprod'] ?? ''),
            ':dias_servida'         => limpiar($_POST['dias_servida'] ?? 0),
            ':resultado_serv'       => limpiar($_POST['resultado_serv'] ?? ''),
            ':proximo_parto'        => limpiar($_POST['proximo_parto'] ?? ''),
            ':ultimo_servicio'      => limpiar($_POST['ultimo_servicio'] ?? ''),
            ':ultima_palpitacion'   => limpiar($_POST['ultima_palpitacion'] ?? ''),
            ':tipo_servicio'        => limpiar($_POST['tipo_servicio'] ?? ''),
            ':toro_sem_utilizado'   => limpiar($_POST['toro_sem_utilizado'] ?? ''),
            ':num_ciclos_obs'       => limpiar($_POST['num_ciclos_obs'] ?? ''),
            ':regul_celos'          => limpiar($_POST['regul_celos'] ?? ''),
            ':diagnostico'          => limpiar($_POST['diagnostico'] ?? ''),
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
        $stmt = $pdo->query("SELECT * FROM vientres_reproductivos ORDER BY id_vientre DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
