<?php
// Configuración de conexión como constantes
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'orientame');

function conexionBBDD() {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conexion->connect_error) {
        error_log("Error de conexión a la base de datos: " . $conexion->connect_error);
        throw new Exception("Error de conexión: " . $conexion->connect_error);
        // O manejar el error según las necesidades de tu aplicación
    }

    return $conexion;
}

function cerrarConexion($conexion) {
    mysqli_close($conexion);
}
?>
