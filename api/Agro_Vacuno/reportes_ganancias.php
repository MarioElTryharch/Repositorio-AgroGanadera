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

$reportes = [];

// 1. Ventas y costos del mes
$sql_ventas_costos = "
    SELECT 
        COALESCE(SUM(CASE WHEN tipo_movimiento = 'venta' THEN valor_total ELSE 0 END), 0) as ventas_mes,
        COALESCE(SUM(CASE WHEN tipo_movimiento IN ('entrada', 'compra') THEN valor_total ELSE 0 END), 0) as costos_mes
    FROM inventario_movimientos 
    WHERE MONTH(fecha_movimiento) = MONTH(CURRENT_DATE())
    AND YEAR(fecha_movimiento) = YEAR(CURRENT_DATE())
    AND estado = 'activo'
";
$result = $conn->query($sql_ventas_costos);
$datos = $result->fetch_assoc();

$reportes['ventas_mes'] = $datos['ventas_mes'] ?? 0;
$reportes['costos_mes'] = $datos['costos_mes'] ?? 0;
$reportes['ganancia_bruta'] = $reportes['ventas_mes'] - $reportes['costos_mes'];
$reportes['margen_ganancia'] = $reportes['ventas_mes'] > 0 ? ($reportes['ganancia_bruta'] / $reportes['ventas_mes']) * 100 : 0;

// 2. Evolución de ventas vs costos (últimos 6 meses)
$sql_evolucion = "
    SELECT 
        DATE_FORMAT(fecha_movimiento, '%Y-%m') as mes,
        SUM(CASE WHEN tipo_movimiento = 'venta' THEN valor_total ELSE 0 END) as ventas,
        SUM(CASE WHEN tipo_movimiento IN ('entrada', 'compra') THEN valor_total ELSE 0 END) as costos
    FROM inventario_movimientos 
    WHERE fecha_movimiento >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
    AND estado = 'activo'
    GROUP BY DATE_FORMAT(fecha_movimiento, '%Y-%m')
    ORDER BY mes ASC
";
$result = $conn->query($sql_evolucion);
$reportes['evolucion'] = [];
while($row = $result->fetch_assoc()) {
    $reportes['evolucion'][] = $row;
}

// 3. Top 10 productos más rentables
$sql_top_productos = "
    SELECT 
        ip.nombre_producto,
        ip.codigo_producto,
        ip.categoria,
        SUM(CASE WHEN im.tipo_movimiento = 'venta' THEN im.cantidad ELSE 0 END) as unidades_vendidas,
        SUM(CASE WHEN im.tipo_movimiento = 'venta' THEN im.valor_total ELSE 0 END) as ventas_totales,
        SUM(CASE WHEN im.tipo_movimiento = 'venta' THEN im.cantidad * (
            SELECT precio_unitario 
            FROM inventario_movimientos im2 
            WHERE im2.producto_id = ip.id 
            AND im2.tipo_movimiento IN ('entrada', 'compra')
            AND im2.fecha_movimiento <= im.fecha_movimiento
            AND im2.estado = 'activo'
            ORDER BY im2.fecha_movimiento DESC 
            LIMIT 1
        ) ELSE 0 END) as costos_totales,
        AVG(ip.precio_venta) as precio_venta_promedio,
        AVG(ip.precio_costo) as precio_costo_promedio
    FROM inventario_productos ip
    LEFT JOIN inventario_movimientos im ON ip.id = im.producto_id AND im.tipo_movimiento = 'venta' AND im.estado = 'activo'
    WHERE ip.estado = 'activo'
    GROUP BY ip.id, ip.nombre_producto, ip.codigo_producto, ip.categoria
    HAVING ventas_totales > 0
    ORDER BY (ventas_totales - costos_totales) DESC
    LIMIT 10
";
$result = $conn->query($sql_top_productos);
$reportes['top_productos'] = [];
while($row = $result->fetch_assoc()) {
    $row['ganancia'] = $row['ventas_totales'] - $row['costos_totales'];
    $row['margen'] = $row['ventas_totales'] > 0 ? ($row['ganancia'] / $row['ventas_totales']) * 100 : 0;
    $row['rotacion'] = $row['unidades_vendidas'] / 6; // Promedio mensual de los últimos 6 meses
    $reportes['top_productos'][] = $row;
}

// 4. Rotación por categoría
$sql_rotacion_categorias = "
    SELECT 
        ip.categoria,
        SUM(CASE WHEN im.tipo_movimiento = 'venta' THEN im.cantidad ELSE 0 END) as unidades_vendidas,
        SUM(CASE WHEN im.tipo_movimiento = 'venta' THEN im.valor_total ELSE 0 END) as ventas_totales,
        COUNT(DISTINCT ip.id) as cantidad_productos
    FROM inventario_productos ip
    LEFT JOIN inventario_movimientos im ON ip.id = im.producto_id AND im.tipo_movimiento = 'venta' AND im.estado = 'activo'
    WHERE ip.estado = 'activo'
    GROUP BY ip.categoria
";
$result = $conn->query($sql_rotacion_categorias);
$reportes['rotacion_categorias'] = [];
while($row = $result->fetch_assoc()) {
    $reportes['rotacion_categorias'][] = $row;
}

$conn->close();

echo json_encode($reportes);
?>