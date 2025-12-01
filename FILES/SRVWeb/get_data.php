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
$sql = "SELECT
    register_id AS id,
    name AS nom,
    institution_name AS Institut,
    created AS creat,
    modified AS modificat,
    addresses_road_id AS id_carrer,
    addresses_road_name AS nom_carrer,
    addresses_start_street_number AS num_inici,
    addresses_end_street_number AS num_final,
    addresses_neighborhood_id AS id_barri,
    addresses_neighborhood_name AS nom_barri,
    addresses_district_id AS id_districte,
    addresses_district_name AS nom_districte,
    addresses_zip_code AS CP,
    addresses_town AS ciutat,
    addresses_main_address AS direccio_main,
    values_id AS id_valor,
    values_attribute_id AS id_atributo,
    values_category AS categoria,
    values_value AS telefono,
    values_outstanding AS excepcional,
    values_description AS descripcion,
    secondary_filters_id AS id_secundari,
    secondary_filters_name AS nom_secundari,
    secondary_filters_fullpath AS ruta,
    secondary_filters_tree AS filtro,
    secondary_filters_asia_id AS asia_id,
    geo_epgs_25831_x AS geolocalizacio_X,
    geo_epgs_25831_y AS geolocalizacio_Y,
    geo_epgs_4326_lat AS latitut,
    geo_epgs_4326_lon AS longitud
FROM equipaments";
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
