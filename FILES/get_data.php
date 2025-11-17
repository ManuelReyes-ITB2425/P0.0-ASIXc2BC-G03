<?php
header('Content-Type: application/json');
$servidor_remoto = '192.168.9.5';
$usuario_db = 'bchecker';
$password_db = 'bchecker121';
$nombre_db = 'educacio_bcn';

$conexion = new mysqli($servidor_remoto, $usuario_db, $password_db, $nombre_db);

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Falló la conexión a la BBDD: ' . $conexion->connect_error]));
}

$sql = "SELECT id, nombre, email FROM usuarios LIMIT 10"; 

$resultado = $conexion->query($sql);

$datos = [];

if ($resultado && $resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
} else if ($resultado) {
} else {
    die(json_encode(['error' => 'Error en la consulta SQL: ' . $conexion->error]));
}

$conexion->close();
echo json_encode($datos);

?>

<?php
header('Content-Type: application/json');
$servidor_remoto = '192.168.9.5';
$usuario_db = 'bchecker';
$password_db = 'bchecker121';
$nombre_db = 'educacio_bcn';

$conexion = new mysqli($servidor_remoto, $usuario_db, $password_db, $nombre_db);

if ($conexion->connect_error) {
    die(json_encode(['error' => 'Falló la conexión a la BBDD: ' . $conexion->connect_error])>
} 

$sql = "SELECT id, nombre, email FROM usuarios LIMIT 10"; 

$resultado = $conexion->query($sql);

$datos = [];

if ($resultado === false) { 
    die(json_encode(['error' => 'Error en la consulta SQL: ' . $conexion->error]));
}

while($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

$conexion->close();
echo json_encode($datos);

?>
