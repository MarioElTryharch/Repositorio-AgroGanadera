<?php
header('Content-Type: application/json');
include '../../../../config/database.php';

$query = "SELECT * FROM vientres WHERE estado = 'activo'";
$result = $conn->query($query);

$vientres = [];
while($row = $result->fetch_assoc()) {
    $vientres[] = $row;
}

echo json_encode($vientres);
?>