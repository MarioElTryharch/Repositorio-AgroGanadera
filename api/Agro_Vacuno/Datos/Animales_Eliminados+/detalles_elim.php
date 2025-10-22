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
        $sql = "INSERT INTO detalles_eliminacion (motivo_principal_elim, causa_especifica_elim, diagnostico_veterinario_elim, trata_aplicados_elim, respons_eliminacion, metodo_eliminacion, observ_adici_elim)
                VALUES (:motivo_principal_elim, :causa_especifica_elim, :diagnostico_veterinario_elim, :trata_aplicados_elim, :respons_eliminacion, :metodo_eliminacion, :observ_adici_elim)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':motivo_principal_elim'            => limpiar($_POST['motivo_principal_elim'] ?? ''),
            ':causa_especifica_elim'            => limpiar($_POST['causa_especifica_elim'] ?? ''),
            ':diagnostico_veterinario_elim'     => limpiar($_POST['diagnostico_veterinario_elim'] ?? ''),
            ':trata_aplicados_elim'             => limpiar($_POST['trata_aplicados_elim'] ?? ''),
            ':respons_eliminacion'              => limpiar($_POST['respons_eliminacion'] ?? ''),
            ':metodo_eliminacion'               => limpiar($_POST['metodo_eliminacion'] ?? ''),
            ':observ_adici_elim'                => limpiar($_POST['observ_adici_elim'] ?? ''),
        ]);

        echo json_encode(["success" => true, "message" => "✅ Animal Eliminado agregado correctamente."]);
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
        $stmt = $pdo->query("SELECT * FROM detalles_eliminacion ORDER BY id_elimanado DESC");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    } catch (PDOException $e) {
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}
?>
