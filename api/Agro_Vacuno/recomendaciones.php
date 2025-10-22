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

$recomendaciones = [];

// 1. Recomendación: Vacunación pendiente
$sql_vacunacion = "
    SELECT COUNT(*) as total_pendientes
    FROM (
        SELECT codigo_id_animal 
        FROM vientres 
        WHERE (ult_desparas IS NULL OR 
               DATE_ADD(ult_desparas, INTERVAL 90 DAY) <= NOW() OR
               vacuna_dia = 'incompleto' OR 
               vacuna_dia = 'ninguna')
              AND estado_prod != 'eliminado'
        
        UNION ALL
        
        SELECT codigo_identificacion_cria 
        FROM animales_cria 
        WHERE (proxima_desparasitacion_cria IS NOT NULL AND 
               proxima_desparasitacion_cria <= NOW()) OR
              vacunaciones_cria = 'incompleto' OR
              vacunaciones_cria = 'ninguna'
    ) as pendientes
";

$result_vacunacion = $conn->query($sql_vacunacion);
if ($result_vacunacion && $row = $result_vacunacion->fetch_assoc()) {
    if ($row['total_pendientes'] > 0) {
        $recomendaciones[] = [
            'titulo' => 'Vacunación Pendiente',
            'descripcion' => $row['total_pendientes'] . ' animales tienen vacunas o desparasitaciones atrasadas.',
            'icono' => 'fa-exclamation-triangle',
            'accion' => 'Ver detalles',
            'funcion' => 'filtrarVacunacionPendiente()',
            'prioridad' => 'alta'
        ];
    }
}

// 2. Recomendación: Animales inseminados recientemente
$sql_inseminados = "
    SELECT COUNT(*) as total_inseminados
    FROM servicios_reproductivos 
    WHERE tipo_servicio = 'inseminacion-artificial'
    AND fecha_servicio >= DATE_SUB(NOW(), INTERVAL 30 DAY)
";

$result_inseminados = $conn->query($sql_inseminados);
if ($result_inseminados && $row = $result_inseminados->fetch_assoc()) {
    if ($row['total_inseminados'] > 0) {
        $recomendaciones[] = [
            'titulo' => 'Inseminaciones Recientes',
            'descripcion' => $row['total_inseminados'] . ' animales fueron inseminados en los últimos 30 días.',
            'icono' => 'fa-syringe',
            'accion' => 'Ver registro',
            'funcion' => 'filtrarInseminados()',
            'prioridad' => 'media'
        ];
    }
}

// 3. Recomendación: Producción láctea vs clima
$sql_produccion = "
    SELECT 
        AVG(produccion_leche) as produccion_promedio,
        COUNT(*) as total_registros
    FROM produccion_lactea 
    WHERE fecha >= DATE_SUB(NOW(), INTERVAL 7 DAY)
";

$result_produccion = $conn->query($sql_produccion);
if ($result_produccion && $row = $result_produccion->fetch_assoc()) {
    if ($row['total_registros'] > 0 && $row['produccion_promedio'] < 20) {
        $recomendaciones[] = [
            'titulo' => 'Producción Láctea',
            'descripcion' => 'Producción promedio baja (' . round($row['produccion_promedio'], 1) . ' L/vaca/día). Revise condiciones climáticas.',
            'icono' => 'fa-chart-line',
            'accion' => 'Analizar',
            'funcion' => 'mostrarAnalisisClima()',
            'prioridad' => 'media'
        ];
    }
}

// 4. Recomendación: Vientres próximos al parto
$sql_partos = "
    SELECT COUNT(*) as total_proximos_partos
    FROM vientres 
    WHERE condicion_reprod = 'preñada'
    AND proximo_parto IS NOT NULL
    AND proximo_parto <= DATE_ADD(NOW(), INTERVAL 30 DAY)
    AND proximo_parto >= NOW()
";

$result_partos = $conn->query($sql_partos);
if ($result_partos && $row = $result_partos->fetch_assoc()) {
    if ($row['total_proximos_partos'] > 0) {
        $recomendaciones[] = [
            'titulo' => 'Partos Próximos',
            'descripcion' => $row['total_proximos_partos'] . ' vientres están próximos al parto (≤ 30 días).',
            'icono' => 'fa-baby',
            'accion' => 'Preparar áreas',
            'funcion' => 'alert(\'Preparar corrales de maternidad y equipos de parto\')',
            'prioridad' => 'alta'
        ];
    }
}

// 5. Recomendación: Animales con bajo peso
$sql_peso = "
    SELECT COUNT(*) as total_bajo_peso
    FROM (
        SELECT codigo_id_animal, peso_act, condi_corp
        FROM vientres 
        WHERE (peso_act IS NOT NULL AND peso_act < 400) 
           OR condi_corp IN ('1', '2')
           AND estado_prod != 'eliminado'
        
        UNION ALL
        
        SELECT codigo_identificacion_cria, peso_actual_cria, condicion_corporal_cria
        FROM animales_cria 
        WHERE (peso_actual_cria IS NOT NULL AND peso_actual_cria < 200)
           OR condicion_corporal_cria IN ('1', '2')
    ) as bajo_peso
";

$result_peso = $conn->query($sql_peso);
if ($result_peso && $row = $result_peso->fetch_assoc()) {
    if ($row['total_bajo_peso'] > 0) {
        $recomendaciones[] = [
            'titulo' => 'Condición Corporal',
            'descripcion' => $row['total_bajo_peso'] . ' animales presentan bajo peso o condición corporal deficiente.',
            'icono' => 'fa-weight-scale',
            'accion' => 'Revisar alimentación',
            'funcion' => 'alert(\'Revisar protocolos de alimentación y suplementación\')',
            'prioridad' => 'media'
        ];
    }
}

// 6. Recomendación: Tratamientos en curso
$sql_tratamientos = "
    SELECT COUNT(*) as total_tratamientos
    FROM tratamientos_sanitarios 
    WHERE estado_tratamiento = 'en-progreso'
    AND fecha_inicio >= DATE_SUB(NOW(), INTERVAL 7 DAY)
";

$result_tratamientos = $conn->query($sql_tratamientos);
if ($result_tratamientos && $row = $result_tratamientos->fetch_assoc()) {
    if ($row['total_tratamientos'] > 0) {
        $recomendaciones[] = [
            'titulo' => 'Tratamientos Activos',
            'descripcion' => $row['total_tratamientos'] . ' tratamientos médicos están en curso.',
            'icono' => 'fa-stethoscope',
            'accion' => 'Monitorear',
            'funcion' => 'alert(\'Monitorear evolución de animales en tratamiento\')',
            'prioridad' => 'media'
        ];
    }
}

$conn->close();

// Ordenar recomendaciones por prioridad
usort($recomendaciones, function($a, $b) {
    $prioridades = ['alta' => 3, 'media' => 2, 'baja' => 1];
    return $prioridades[$b['prioridad']] - $prioridades[$a['prioridad']];
});

echo json_encode($recomendaciones);
?>