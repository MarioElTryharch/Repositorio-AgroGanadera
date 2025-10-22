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

// Obtener datos del POST
$input = json_decode(file_get_contents('php://input'), true);

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

$response = ['success' => false, 'message' => ''];

try {
    // Preparar datos
    $tipo_movimiento = $conn->real_escape_string($input['tipo_movimiento']);
    $fecha_movimiento = $conn->real_escape_string($input['fecha_movimiento']);
    $categoria = $conn->real_escape_string($input['categoria']);
    $producto = $conn->real_escape_string($input['producto']);
    $cantidad = floatval($input['cantidad']);
    $unidad_medida = $conn->real_escape_string($input['unidad_medida']);
    $proveedor_cliente = $conn->real_escape_string($input['proveedor_cliente'] ?? '');
    $numero_factura = $conn->real_escape_string($input['numero_factura'] ?? '');
    $precio_unitario = floatval($input['precio_unitario'] ?? 0);
    $valor_total = floatval($input['valor_total'] ?? 0);
    $motivo_movimiento = $conn->real_escape_string($input['motivo_movimiento']);
    $responsable_movimiento = $conn->real_escape_string($input['responsable_movimiento']);
    $lote_producto = $conn->real_escape_string($input['lote_producto'] ?? '');
    $fecha_vencimiento = $conn->real_escape_string($input['fecha_vencimiento'] ?? null);
    $ubicacion_almacen = $conn->real_escape_string($input['ubicacion_almacen'] ?? '');
    $condicion_almacenamiento = $conn->real_escape_string($input['condicion_almacenamiento'] ?? '');
    $observaciones = $conn->real_escape_string($input['observaciones'] ?? '');
    
    // Insertar movimiento
    $sql = "INSERT INTO inventario_movimientos (
        tipo_movimiento, fecha_movimiento, categoria, producto, 
        cantidad, unidad_medida, proveedor_cliente, numero_factura,
        precio_unitario, valor_total, motivo_movimiento, responsable_movimiento,
        lote_producto, fecha_vencimiento, ubicacion_almacen, condicion_almacenamiento,
        observaciones, estado, fecha_registro
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'activo', NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssdssddssssssss", 
        $tipo_movimiento, $fecha_movimiento, $categoria, $producto,
        $cantidad, $unidad_medida, $proveedor_cliente, $numero_factura,
        $precio_unitario, $valor_total, $motivo_movimiento, $responsable_movimiento,
        $lote_producto, $fecha_vencimiento, $ubicacion_almacen, $condicion_almacenamiento,
        $observaciones
    );
    
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Movimiento de inventario registrado correctamente'];
    } else {
        $response = ['success' => false, 'message' => 'Error al registrar movimiento: ' . $stmt->error];
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

$conn->close();
echo json_encode($response);
?>