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

// Obtener parámetros
$mes = $_GET['mes'] ?? date('Y-m');
$tipoClima = $_GET['clima'] ?? 'temperatura';

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida: " . $conn->connect_error]));
}

// Consulta para producción de leche del mes
$sql_produccion = "
    SELECT 
        DATE(fecha) as dia,
        AVG(produccion_leche) as produccion_promedio,
        COUNT(DISTINCT codigo_vaca) as cantidad_vacas
    FROM produccion_lactea 
    WHERE DATE_FORMAT(fecha, '%Y-%m') = ?
    GROUP BY DATE(fecha)
    ORDER BY dia
";

$stmt = $conn->prepare($sql_produccion);
$stmt->bind_param("s", $mes);
$stmt->execute();
$result_produccion = $stmt->get_result();

$produccion_data = [];
$dias = [];
$produccion = [];

if ($result_produccion->num_rows > 0) {
    while($row = $result_produccion->fetch_assoc()) {
        $dias[] = date('d/m', strtotime($row['dia']));
        $produccion[] = floatval($row['produccion_promedio']);
        $produccion_data[$row['dia']] = $row;
    }
}

// Consulta para datos climáticos (simulados basados en patrones estacionales)
$clima_data = [];
$clima_values = [];

// Generar datos climáticos simulados basados en el mes y tipo de clima
$mes_numero = intval(date('m', strtotime($mes . '-01')));
$dias_mes = cal_days_in_month(CAL_GREGORIAN, $mes_numero, intval(date('Y', strtotime($mes . '-01'))));

for ($i = 1; $i <= $dias_mes; $i++) {
    $fecha = $mes . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
    
    // Simular datos climáticos según el tipo y mes
    switch ($tipoClima) {
        case 'temperatura':
            // Temperaturas más altas en verano, más bajas en invierno
            $base_temp = 20; // Temperatura base
            $variacion_estacional = sin(($mes_numero - 1) * M_PI / 6) * 10; // Variación estacional ±10°C
            $variacion_diaria = rand(-5, 5); // Variación diaria aleatoria
            $valor = $base_temp + $variacion_estacional + $variacion_diaria;
            break;
            
        case 'humedad':
            // Humedad más alta en meses lluviosos
            $base_humidity = 60;
            $variacion_estacional = sin(($mes_numero - 1) * M_PI / 6 + M_PI/2) * 20; // Desfasado 90° de temperatura
            $variacion_diaria = rand(-10, 10);
            $valor = $base_humidity + $variacion_estacional + $variacion_diaria;
            break;
            
        case 'precipitacion':
            // Precipitación aleatoria con mayor probabilidad en meses lluviosos
            $probabilidad_lluvia = sin(($mes_numero - 1) * M_PI / 6 + M_PI/2) * 0.3 + 0.2; // 20-50% probabilidad
            $valor = (rand(0, 100) / 100 < $probabilidad_lluvia) ? rand(5, 50) : 0;
            break;
            
        default:
            $valor = 0;
    }
    
    $clima_values[] = round($valor, 1);
}

// Si no hay datos de producción, generar datos de ejemplo
if (empty($produccion)) {
    for ($i = 1; $i <= $dias_mes; $i++) {
        $dias[] = str_pad($i, 2, '0', STR_PAD_LEFT) . '/' . str_pad($mes_numero, 2, '0', STR_PAD_LEFT);
        
        // Simular producción con correlación negativa con temperatura
        $produccion_base = 25; // Litros por vaca
        $impacto_clima = 0;
        
        if ($tipoClima === 'temperatura') {
            $impacto_clima = -0.5 * ($clima_values[$i-1] - 20); // -0.5L por cada grado sobre 20°C
        } elseif ($tipoClima === 'humedad') {
            $impacto_clima = -0.1 * ($clima_values[$i-1] - 60); // -0.1L por cada % sobre 60%
        } elseif ($tipoClima === 'precipitacion') {
            $impacto_clima = -0.2 * $clima_values[$i-1]; // -0.2L por cada mm de lluvia
        }
        
        $variacion_aleatoria = rand(-3, 3);
        $produccion[] = max(15, $produccion_base + $impacto_clima + $variacion_aleatoria);
    }
}

$conn->close();

// Preparar respuesta
$response = [
    'dias' => $dias,
    'produccion' => $produccion,
    'clima' => $clima_values,
    'tipoClima' => $tipoClima,
    'mes' => $mes,
    'correlacion' => calcularCorrelacion($produccion, $clima_values)
];

echo json_encode($response);

// Función para calcular correlación entre producción y clima
function calcularCorrelacion($produccion, $clima) {
    $n = count($produccion);
    if ($n <= 1) return 0;
    
    $sum_x = array_sum($produccion);
    $sum_y = array_sum($clima);
    $sum_xy = 0;
    $sum_x2 = 0;
    $sum_y2 = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $sum_xy += $produccion[$i] * $clima[$i];
        $sum_x2 += $produccion[$i] * $produccion[$i];
        $sum_y2 += $clima[$i] * $clima[$i];
    }
    
    $numerador = $n * $sum_xy - $sum_x * $sum_y;
    $denominador = sqrt(($n * $sum_x2 - $sum_x * $sum_x) * ($n * $sum_y2 - $sum_y * $sum_y));
    
    return $denominador != 0 ? $numerador / $denominador : 0;
}
?>