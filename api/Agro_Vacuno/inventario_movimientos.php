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
$tipo_movimiento = $_GET['tipo'] ?? 'todos';
$categoria = $_GET['categoria_producto'] ?? 'todos';
$mes = $_GET['mes'] ?? date('Y-m');

// Construir consulta base
$sql = "
    SELECT 
        im.id,
        im.fecha_movimiento,
        im.producto,
        im.categoria_producto,
        im.tipo_movimiento,
        im.cantidad,
        im.unidad_medida,
        im.proveedor_cliente,
        im.motivo_movimiento,
        im.precio_unitario,
        im.valor_total,
        im.responsable_movimiento,
        im.lote_producto,
        im.fecha_vencimiento,
        im.ubicacion_almacen,
        im.condicion_almacenamiento,
        im.observaciones,
        im.estado,
        im.fecha_registro,
        (SELECT SUM(cantidad) FROM inventario_movimientos im2 
         WHERE im2.producto = im.producto 
         AND im2.fecha_movimiento <= im.fecha_movimiento
         AND im2.tipo_movimiento IN ('entrada', 'ajuste')
         AND im2.estado = 'activo') - 
        (SELECT SUM(cantidad) FROM inventario_movimientos im2 
         WHERE im2.producto = im.producto 
         AND im2.fecha_movimiento <= im.fecha_movimiento
         AND im2.tipo_movimiento IN ('salida', 'ajuste')
         AND im2.estado = 'activo') as stock_actual
    FROM inventario_movimientos im
    WHERE im.estado = 'activo'
";

// Aplicar filtros
if ($tipo_movimiento !== 'todos') {
    $sql .= " AND im.tipo_movimiento = '" . $conn->real_escape_string($tipo_movimiento) . "'";
}

if ($categoria !== 'todos') {
    $sql .= " AND im.categoria_producto = '" . $conn->real_escape_string($categoria) . "'";
}

$sql .= " ORDER BY im.fecha_movimiento DESC, im.id DESC";

$result = $conn->query($sql);

$movimientos = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Formatear fechas
        $row['fecha_movimiento'] = date('d/m/Y H:i', strtotime($row['fecha_movimiento']));
        if ($row['fecha_vencimiento']) {
            $row['fecha_vencimiento'] = date('d/m/Y', strtotime($row['fecha_vencimiento']));
        }
        
        // Calcular días hasta vencimiento
        if ($row['fecha_vencimiento'] && $row['stock_actual'] > 0) {
            $fecha_vencimiento = DateTime::createFromFormat('d/m/Y', $row['fecha_vencimiento']);
            $hoy = new DateTime();
            $dias_vencimiento = $hoy->diff($fecha_vencimiento)->days;
            $row['dias_vencimiento'] = $dias_vencimiento;
            $row['alerta_vencimiento'] = $dias_vencimiento <= 30 ? 'critico' : ($dias_vencimiento <= 60 ? 'advertencia' : 'normal');
        } else {
            $row['dias_vencimiento'] = null;
            $row['alerta_vencimiento'] = 'normal';
        }
        
        $movimientos[] = $row;
    }
}

$conn->close();

echo json_encode($movimientos);
?>