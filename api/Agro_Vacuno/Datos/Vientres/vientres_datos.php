<?php
// 🔹 Forzar respuesta JSON
header('Content-Type: application/json');

// 🔹 Mostrar errores (solo durante desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🔹 Conexión a la base de datos
require_once __DIR__ . '/../../../../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    echo json_encode(["success" => false, "message" => "❌ No se pudo conectar a la base de datos."]);
    exit;
}

// 🔹 Función para limpiar datos
function limpiar($data) {
    return htmlspecialchars(trim($data));
}

// 🔹 INSERTAR NUEVO REGISTRO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $fecha_nac = limpiar($_POST['fecha_nac'] ?? '');
        $fecha_ingr = limpiar($_POST['fecha_ingr'] ?? '');

        // Calcular edad en meses
        $edad_meses = 0;
        if ($fecha_nac) {
            $fechaNacimiento = new DateTime($fecha_nac);
            $fechaActual = new DateTime($fecha_ingr ?: 'now');
            $interval = $fechaNacimiento->diff($fechaActual);
            $edad_meses = ($interval->y * 12) + $interval->m;
        }

        // Insertar datos
        $sql = "INSERT INTO vientres_basicos 
                (nombre_animal, tipo_animal, codigo_id_animal, fecha_nac, cruce_animal, corral_animal, grupo_animal, edad_meses, raza_princ, color_animal, lote_animal, peso_act, condi_corp, fecha_ingr, origen_animal)
                VALUES (:nombre_animal, :tipo_animal, :codigo_id_animal, :fecha_nac, :cruce_animal, :corral_animal, :grupo_animal, :edad_meses, :raza_princ, :color_animal, :lote_animal, :peso_act, :condi_corp, :fecha_ingr, :origen_animal)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre_animal'        => limpiar($_POST['nombre_animal'] ?? ''),
            ':tipo_animal'          => limpiar($_POST['tipo_animal'] ?? ''),
            ':codigo_id_animal'     => limpiar($_POST['codigo_id_animal'] ?? ''),
            ':fecha_nac'            => $fecha_nac,
            ':cruce_animal'         => limpiar($_POST['cruce_animal'] ?? ''),
            ':corral_animal'        => limpiar($_POST['corral_animal'] ?? ''),
            ':grupo_animal'         => limpiar($_POST['grupo_animal'] ?? ''),
            ':edad_meses'           => $edad_meses,
            ':raza_princ'           => limpiar($_POST['raza_princ'] ?? ''),
            ':color_animal'         => limpiar($_POST['color_animal'] ?? ''),
            ':lote_animal'          => limpiar($_POST['lote_animal'] ?? ''),
            ':peso_act'             => limpiar($_POST['peso_act'] ?? 0),
            ':condi_corp'           => limpiar($_POST['condi_corp'] ?? ''),
            ':fecha_ingr'           => $fecha_ingr,
            ':origen_animal'        => limpiar($_POST['origen_animal'] ?? 0)
        ]);

        // Obtener el contador actualizado
        $stmtCount = $pdo->query("SELECT COUNT(*) AS total FROM vientres_basicos");
        $total = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            "success" => true,
            "message" => "✅ Vientre agregado correctamente.",
            "total"   => $total
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "❌ Error al guardar: " . $e->getMessage()]);
        exit;
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "❌ Error inesperado: " . $e->getMessage()]);
        exit;
    }
}

// 🔹 OBTENER CONTADOR (GET)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) AS total FROM vientres_basicos");
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "total" => $data['total']]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "total" => 0, "message" => "❌ Error al obtener contador: " . $e->getMessage()]);
    }
    exit;
}

// 🔹 Si no es POST ni GET
echo json_encode(["success" => false, "message" => "❌ Método no permitido."]);
exit;
