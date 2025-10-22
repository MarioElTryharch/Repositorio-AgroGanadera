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

// Obtener parámetros de filtrado
$busqueda = $_GET['busqueda'] ?? '';
$categoria = $_GET['categoria'] ?? 'todas';
$estado = $_GET['estado'] ?? 'todos';

// Consulta para obtener el inventario detallado
$sql = "
    SELECT 
        ip.id,
        ip.codigo_producto,
        ip.nombre_producto,
        ip.categoria,
        ip.descripcion,
        ip.unidad_medida,
        ip.stock_minimo,
        ip.stock_maximo,
        ip.precio_costo,
        ip.precio_venta,
        ip.proveedor_principal,
        ip.ubicacion_almacen,
        ip.estado as estado_producto,
        ip.fecha_creacion,
        
        -- Calcular stock actual
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
        ), 0) as stock_actual,
        
        -- Calcular valor en stock
        COALESCE((
            SELECT SUM(
                CASE 
                    WHEN tipo_movimiento IN ('entrada', 'compra', 'produccion', 'ajuste_positivo') THEN cantidad * precio_unitario
                    WHEN tipo_movimiento IN ('salida', 'venta', 'consumo', 'ajuste_negativo') THEN -cantidad * precio_unitario
                    ELSE 0
                END
            )
            FROM inventario_movimientos 
            WHERE producto_id = ip.id 
            AND estado = 'activo'
        ), 0) as valor_stock,
        
        -- Último movimiento
        (
            SELECT CONCAT(DATE_FORMAT(fecha_movimiento, '%d/%m/%Y'), ' - ', tipo_movimiento)
            FROM inventario_movimientos 
            WHERE producto_id = ip.id 
            AND estado = 'activo'
            ORDER BY fecha_movimiento DESC 
            LIMIT 1
        ) as ultimo_movimiento,
        
        -- Ventas del mes actual
        COALESCE((
            SELECT SUM(cantidad)
            FROM inventario_movimientos 
            WHERE producto_id = ip.id 
            AND tipo_movimiento = 'venta'
            AND MONTH(fecha_movimiento) = MONTH(CURRENT_DATE())
            AND YEAR(fecha_movimiento) = YEAR(CURRENT_DATE())
            AND estado = 'activo'
        ), 0) as ventas_mes_actual,
        
        -- Ganancia total del producto
        COALESCE((
            SELECT SUM((precio_venta - precio_unitario) * cantidad)
            FROM inventario_movimientos 
            WHERE producto_id = ip.id 
            AND tipo_movimiento = 'venta'
            AND estado = 'activo'
        ), 0) as ganancia_total
        
    FROM inventario_productos ip
    WHERE ip.estado = 'activo'
";

// Aplicar filtros
if (!empty($busqueda)) {
    $sql .= " AND (ip.nombre_producto LIKE '%" . $conn->real_escape_string($busqueda) . "%' 
                OR ip.codigo_producto LIKE '%" . $conn->real_escape_string($busqueda) . "%')";
}

if ($categoria !== 'todas') {
    $sql .= " AND ip.categoria = '" . $conn->real_escape_string($categoria) . "'";
}

if ($estado !== 'todos') {
    if ($estado === 'normal') {
        $sql .= " HAVING stock_actual > stock_minimo";
    } elseif ($estado === 'bajo') {
        $sql .= " HAVING stock_actual <= stock_minimo AND stock_actual > 0";
    } elseif ($estado === 'agotado') {
        $sql .= " HAVING stock_actual <= 0";
    }
}

$sql .= " ORDER BY ip.nombre_producto ASC";

$result = $conn->query($sql);

$inventario = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Determinar estado del stock
        if ($row['stock_actual'] <= 0) {
            $row['estado_stock'] = 'agotado';
            $row['clase_estado'] = 'danger';
            $row['texto_estado'] = 'Agotado';
        } elseif ($row['stock_actual'] <= $row['stock_minimo']) {
            $row['estado_stock'] = 'bajo';
            $row['clase_estado'] = 'warning';
            $row['texto_estado'] = 'Stock Bajo';
        } else {
            $row['estado_stock'] = 'normal';
            $row['clase_estado'] = 'success';
            $row['texto_estado'] = 'Normal';
        }
        
        // Calcular margen de ganancia
        if ($row['precio_costo'] > 0) {
            $row['margen_ganancia'] = (($row['precio_venta'] - $row['precio_costo']) / $row['precio_costo']) * 100;
        } else {
            $row['margen_ganancia'] = 0;
        }
        
        $inventario[] = $row;
    }
}

$conn->close();

echo json_encode($inventario);
?>