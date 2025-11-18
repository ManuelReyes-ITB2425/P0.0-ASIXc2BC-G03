<?php
// 1. INICIALIZACIÓN Y CONFIGURACIÓN
header('Content-Type: application/json');

$servidor_remoto = '192.168.9.5';
$usuario_db = 'bchecker';
$password_db = 'bchecker121';
$nombre_db = 'educacio_bcn';

// 2. CONEXIÓN A LA BBDD
$conexion = @new mysqli($servidor_remoto, $usuario_db, $password_db, $nombre_db);

// 3. MANEJO DEL ERROR DE CONEXIÓN
if ($conexion->connect_error) {
    die(json_encode(['error' => 'Falló la conexión a la BBDD: ' . $conexion->connect_error]));
}

// 4. CONSULTA SQL CORREGIDA (Usando la tabla 'equipaments' y aliasando las columnas)
$sql = "SELECT register_id AS id, name AS nombre, institution_name AS email FROM equipaments LIMIT 10"; 
$resultado = $conexion->query($sql);
$datos = [];

// 5. MANEJO DEL ERROR DE LA CONSULTA
if ($resultado === false) {
    die(json_encode(['error' => 'Error en la consulta SQL: ' . $conexion->error]));
}

// 6. PROCESAR RESULTADOS
while($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

// 7. CIERRE Y SALIDA
$conexion->close();
echo json_encode($datos);

?>