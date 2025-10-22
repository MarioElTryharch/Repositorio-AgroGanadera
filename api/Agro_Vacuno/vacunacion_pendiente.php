<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de la base de datos
$servername = "localhost";
$username = "root"; // Cambiar por tus credenciales
$password = ""; // Cambiar por tus credenciales
$dbname = "agrobufalino22";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Consulta para obtener animales con vacunas pendientes
$sql = "
    SELECT 
        v.codigo_id_animal as codigo,
        v.nombre_animal as nombre,
        v.vacuna_dia as estado_vacunacion,
        v.ult_desparas as ultima_desparasitacion,
        v.prox_desparas as proxima_desparasitacion,
        DATE_ADD(v.ult_desparas, INTERVAL 90 DAY) as fecha_programada,
        'Desparasitación' as vacuna_pendiente,
        DATEDIFF(NOW(), DATE_ADD(v.ult_desparas, INTERVAL 90 DAY)) as dias_retraso
    FROM vientres v
    WHERE 
        (v.ult_desparas IS NULL OR 
         DATE_ADD(v.ult_desparas, INTERVAL 90 DAY) <= NOW() OR
         v.vacuna_dia = 'incompleto' OR 
         v.vacuna_dia = 'ninguna')
        AND v.estado_prod != 'eliminado'
    
    UNION ALL
    
    SELECT 
        c.codigo_identificacion_cria as codigo,
        c.nombre_cria as nombre,
        c.vacunaciones_cria as estado_vacunacion,
        c.ultima_desparasitacion_cria as ultima_desparasitacion,
        c.proxima_desparasitacion_cria as proxima_desparasitacion,
        c.proxima_desparasitacion_cria as fecha_programada,
        'Desparasitación' as vacuna_pendiente,
        DATEDIFF(NOW(), c.proxima_desparasitacion_cria) as dias_retraso
    FROM animales_cria c
    WHERE 
        (c.proxima_desparasitacion_cria IS NOT NULL AND 
         c.proxima_desparasitacion_cria <= NOW()) OR
        c.vacunaciones_cria = 'incompleto' OR
        c.vacunaciones_cria = 'ninguna'
    
    ORDER BY dias_retraso DESC, fecha_programada ASC
";

$result = $conn->query($sql);

$vacunacion_pendiente = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Formatear fechas
        if ($row['ultima_desparasitacion']) {
            $row['ultima_desparasitacion'] = date('Y-m-d', strtotime($row['ultima_desparasitacion']));
        }
        if ($row['proxima_desparasitacion']) {
            $row['proxima_desparasitacion'] = date('Y-m-d', strtotime($row['proxima_desparasitacion']));
        }
        if ($row['fecha_programada']) {
            $row['fecha_programada'] = date('Y-m-d', strtotime($row['fecha_programada']));
        }
        
        // Calcular días de retraso
        $row['dias_retraso'] = $row['dias_retraso'] > 0 ? $row['dias_retraso'] : 0;
        
        $vacunacion_pendiente[] = $row;
    }
}

$conn->close();

echo json_encode($vacunacion_pendiente);
?>