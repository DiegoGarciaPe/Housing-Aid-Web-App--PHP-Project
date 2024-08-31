<?php
require 'conexionBBDD.php';
$conexion = conexionBBDD();

$idConsulta = isset($_POST['id']) ? $_POST['id'] : '';
$resuelta = isset($_POST['resuelta']) ? $_POST['resuelta'] : '';

if ($idConsulta !== '' && $resuelta !== '') {
    $sql = "UPDATE consultas SET resuelta = ? WHERE ID = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $resuelta, $idConsulta);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
    $stmt->close();
}

$conexion->close();
?>
