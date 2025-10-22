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

// 1. Total de ítems en inventario
$sql_total_items = "
    SELECT COUNT(*) as total_items
    FROM inventario_productos 
    WHERE estado = 'activo'
";
$result = $conn->query($sql_total_items);
$resumen['total_items'] = $result->fetch_assoc()['total_items'] ?? 0;

// 2. Valor total del inventario
$sql_valor_total = "
    SELECT COALESCE(SUM(
        (SELECT SUM(
            CASE 
                WHEN tipo_movimiento IN ('entrada', 'compra', 'produccion', 'ajuste_positivo') THEN cantidad * precio_unitario
                WHEN tipo_movimiento IN ('salida', 'venta', 'consumo', 'ajuste_negativo') THEN -cantidad * precio_unitario
                ELSE 0
            END
        )
        FROM inventario_movimientos 
        WHERE producto_id = ip.id 
        AND estado = 'activo')
    ), 0) as valor_total
    FROM inventario_productos ip
    WHERE ip.estado = 'activo'
";
$result = $conn->query($sql_valor_total);
$resumen['valor_total'] = $result->fetch_assoc()['valor_total'] ?? 0;

// 3. Ítems con stock bajo
$sql_stock_bajo = "
    SELECT COUNT(*) as items_bajo_stock
    FROM (
        SELECT ip.id,
            COALESCE((
                SELECT SUM(
                    CASE 
                        WHEN tipo_movimiento IN ('entrada', 'compra', 'produccion', 'ajuste_positivo') THEN cantidad
                        WHEN tipo_movimiento IN ('salida', 'venta', 'consumo', 'ajuste_negativo') THEN -cantidad
                        ELSE 0
                    END
                )
                FROM inventario_movimientos 
                WHERE producto_id = ip.id 
                AND estado = 'activo'
            ), 0) as stock_actual
        FROM inventario_productos ip
        WHERE ip.estado = 'activo'
    ) as stock
    WHERE stock_actual <= 0
";
$result = $conn->query($sql_stock_bajo);
$resumen['items_bajo_stock'] = $result->fetch_assoc()['items_bajo_stock'] ?? 0;

// 4. Ítems agotados
$sql_agotados = "
    SELECT COUNT(*) as items_agotados
    FROM (
        SELECT ip.id,
            COALESCE((
                SELECT SUM(
                    CASE 
                        WHEN tipo_movimiento IN ('entrada', 'compra', 'produccion', 'ajuste_positivo') THEN cantidad
                        WHEN tipo_movimiento IN ('salida', 'venta', 'consumo', 'ajuste_negativo') THEN -cantidad
                        ELSE 0
                    END
                )
                FROM inventario_movimientos 
                WHERE producto_id = ip.id 
                AND estado = 'activo'
            ), 0) as stock_actual
        FROM inventario_productos ip
        WHERE ip.estado = 'activo'
    ) as stock
    WHERE stock_actual <= 0
";
$result = $conn->query($sql_agotados);
$resumen['items_agotados'] = $result->fetch_assoc()['items_agotados'] ?? 0;

// 5. Rotación mensual (ventas del mes / inventario promedio)
$sql_rotacion = "
    SELECT 
        COALESCE(SUM(
            CASE WHEN im.tipo_movimiento = 'venta' THEN im.cantidad * im.precio_unitario ELSE 0 END
        ), 0) as ventas_mes,
        COALESCE(SUM(
            CASE WHEN im.tipo_movimiento IN ('entrada', 'compra') THEN im.cantidad * im.precio_unitario ELSE 0 END
        ), 0) as compras_mes
    FROM inventario_movimientos im
    WHERE MONTH(im.fecha_movimiento) = MONTH(CURRENT_DATE())
    AND YEAR(im.fecha_movimiento) = YEAR(CURRENT_DATE())
    AND im.estado = 'activo'
";
$result = $conn->query($sql_rotacion);
$datos_mes = $result->fetch_assoc();
$ventas_mes = $datos_mes['ventas_mes'] ?? 0;
$compras_mes = $datos_mes['compras_mes'] ?? 0;

$resumen['rotacion_mensual'] = $resumen['valor_total'] > 0 ? ($ventas_mes / $resumen['valor_total']) * 100 : 0;
$resumen['ganancia_neta_mes'] = $ventas_mes - $compras_mes;

// 6. Movimientos del mes
$sql_movimientos_mes = "
    SELECT 
        tipo_movimiento,
        COUNT(*) as cantidad,
        SUM(cantidad) as total_unidades,
        SUM(cantidad * precio_unitario) as valor_total
    FROM inventario_movimientos 
    WHERE MONTH(fecha_movimiento) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_movimiento) = YEAR(CURRENT_DATE())
    AND estado = 'activo'
    GROUP BY tipo_movimiento
";
$result = $conn->query($sql_movimientos_mes);
$movimientos_mes = [];
while($row = $result->fetch_assoc()) {
    $movimientos_mes[$row['tipo_movimiento']] = $row;
}

$resumen['entradas_mes'] = $movimientos_mes['entrada']['cantidad'] ?? 0;
$resumen['salidas_mes'] = $movimientos_mes['salida']['cantidad'] ?? 0;
$resumen['valor_entradas'] = $movimientos_mes['entrada']['valor_total'] ?? 0;
$resumen['valor_salidas'] = $movimientos_mes['salida']['valor_total'] ?? 0;

$conn->close();

echo json_encode($resumen);
?>