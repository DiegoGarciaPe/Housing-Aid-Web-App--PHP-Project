<?php
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT Nombre, Apellidos, Sexo, Direccion, Fecha_Nacimiento, Nacionalidad, Email, Profesion, Discapacidad, Parado, Estado_Civil, Prestacion_Desempleo, Subsidio_Desempleo, Paro_Cobrado_Agotado, Derecho_Paro, Derecho_Subsidio FROM usuario WHERE id = $id";
    $result = $conexion->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode($user);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}