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

// Obtener parámetros
$fecha_desde = $_GET['fecha_desde'] ?? date('Y-m-01');
$fecha_hasta = $_GET['fecha_hasta'] ?? date('Y-m-t');
$tipo = $_GET['tipo'] ?? 'todos';

// Consulta para movimientos detallados
$sql = "
    SELECT 
        im.id,
        im.fecha_movimiento,
        ip.nombre_producto,
        ip.codigo_producto,
        im.tipo_movimiento,
        im.cantidad,
        im.precio_unitario,
        im.valor_total,
        im.proveedor_cliente,
        im.motivo_movimiento,
        im.numero_documento,
        im.responsable,
        im.observaciones,
        
        -- Stock posterior al movimiento
        (
            SELECT SUM(
                CASE 
                    WHEN tipo_movimiento IN ('entrada', 'compra', 'produccion', 'ajuste_positivo') THEN cantidad
                    WHEN tipo_movimiento IN ('salida', 'venta', 'consumo', 'ajuste_negativo') THEN -cantidad
                    ELSE 0
                END
            )
            FROM inventario_movimientos 
            WHERE producto_id = im.producto_id 
            AND fecha_movimiento <= im.fecha_movimiento
            AND estado = 'activo'
        ) as stock_posterior,
        
        -- Ganancia si es venta
        CASE 
            WHEN im.tipo_movimiento = 'venta' THEN 
                (im.precio_unitario - (
                    SELECT precio_unitario 
                    FROM inventario_movimientos 
                    WHERE producto_id = im.producto_id 
                    AND tipo_movimiento IN ('entrada', 'compra')
                    AND fecha_movimiento <= im.fecha_movimiento
                    AND estado = 'activo'
                    ORDER BY fecha_movimiento DESC 
                    LIMIT 1
                )) * im.cantidad
            ELSE 0
        END as ganancia
        
    FROM inventario_movimientos im
    INNER JOIN inventario_productos ip ON im.producto_id = ip.id
    WHERE im.estado = 'activo'
    AND DATE(im.fecha_movimiento) BETWEEN ? AND ?
";

if ($tipo !== 'todos') {
    $sql .= " AND im.tipo_movimiento = ?";
}

$sql .= " ORDER BY im.fecha_movimiento DESC, im.id DESC";

$stmt = $conn->prepare($sql);

if ($tipo !== 'todos') {
    $stmt->bind_param("sss", $fecha_desde, $fecha_hasta, $tipo);
} else {
    $stmt->bind_param("ss", $fecha_desde, $fecha_hasta);
}

$stmt->execute();
$result = $stmt->get_result();

$movimientos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Formatear fechas
        $row['fecha_movimiento'] = date('d/m/Y H:i', strtotime($row['fecha_movimiento']));
        
        $movimientos[] = $row;
    }
}

$stmt->close();
$conn->close();

echo json_encode($movimientos);
?>