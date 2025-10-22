<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agrobufalino22";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$resumen = [];

// 1. Entradas del mes actual
$sql_entradas = "
    SELECT COALESCE(SUM(cantidad), 0) as total_entradas
    FROM inventario_movimientos 
    WHERE tipo_movimiento = 'entrada'
    AND MONTH(fecha_movimiento) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_movimiento) = YEAR(CURRENT_DATE())
    AND estado = 'activo'
";

$result_entradas = $conn->query($sql_entradas);
$resumen['entradas_mes'] = $result_entradas->fetch_assoc()['total_entradas'] ?? 0;

// 2. Salidas del mes actual
$sql_salidas = "
    SELECT COALESCE(SUM(cantidad), 0) as total_salidas
    FROM inventario_movimientos 
    WHERE tipo_movimiento = 'salida'
    AND MONTH(fecha_movimiento) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_movimiento) = YEAR(CURRENT_DATE())
    AND estado = 'activo'
";

$result_salidas = $conn->query($sql_salidas);
$resumen['salidas_mes'] = $result_salidas->fetch_assoc()['total_salidas'] ?? 0;

// 3. Productos con stock crítico
$sql_stock_critico = "
    SELECT COUNT(DISTINCT producto) as total_critico
    FROM (
        SELECT 
            producto,
            (SELECT SUM(cantidad) FROM inventario_movimientos im2 
             WHERE im2.producto = im.producto 
             AND im2.tipo_movimiento IN ('entrada', 'ajuste')
             AND im2.estado = 'activo') - 
            (SELECT SUM(cantidad) FROM inventario_movimientos im2 
             WHERE im2.producto = im.producto 
             AND im2.tipo_movimiento IN ('salida', 'ajuste')
             AND im2.estado = 'activo') as stock_actual
        FROM inventario_movimientos im
        WHERE im.estado = 'activo'
        GROUP BY producto
    ) as stock
    WHERE stock_actual <= 10
";

$result_stock_critico = $conn->query($sql_stock_critico);
$resumen['stock_critico'] = $result_stock_critico->fetch_assoc()['total_critico'] ?? 0;

// 4. Productos próximos a vencer (≤ 30 días)
$sql_proximos_vencer = "
    SELECT COUNT(DISTINCT producto) as total_proximos_vencer
    FROM inventario_movimientos 
    WHERE fecha_vencimiento IS NOT NULL
    AND fecha_vencimiento <= DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)
    AND fecha_vencimiento >= CURRENT_DATE()
    AND estado = 'activo'
";

$result_proximos_vencer = $conn->query($sql_proximos_vencer);
$resumen['proximos_vencer'] = $result_proximos_vencer->fetch_assoc()['total_proximos_vencer'] ?? 0;

$conn->close();

echo json_encode($resumen);
?>