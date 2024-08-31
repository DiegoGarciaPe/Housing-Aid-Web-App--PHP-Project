<?php
session_start();
if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

require 'conexionBBDD.php';
$conexion = conexionBBDD();

$emailUsuario = $_SESSION["email"];
$idConsulta = $_POST['id_consulta'];
$mensaje = $_POST['mensaje'];
$esSuperusuario = $_POST['es_superusuario'];

// Obtener el ID y el nombre del usuario
$sql = "SELECT id, Nombre FROM usuario WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $emailUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUsuario = $row['id'];
    $nombreUsuario = $row['Nombre'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Insertar el mensaje en la base de datos
$sqlInsert = "INSERT INTO mensajes (id_consulta, id_usuario, mensaje, es_superusuario) VALUES (?, ?, ?, ?)";
$stmtInsert = $conexion->prepare($sqlInsert);
$stmtInsert->bind_param("iisi", $idConsulta, $idUsuario, $mensaje, $esSuperusuario);

if ($stmtInsert->execute()) {
    // Si el mensaje es enviado por un superusuario, comprobar si hay que asignarlo a la consulta
    if ($esSuperusuario == 1) {
        // Comprobar si la consulta ya tiene un superusuario asignado
        $sqlCheckSuperusuario = "SELECT nombre_superusuario FROM consultas WHERE ID = ?";
        $stmtCheckSuperusuario = $conexion->prepare($sqlCheckSuperusuario);
        $stmtCheckSuperusuario->bind_param("i", $idConsulta);
        $stmtCheckSuperusuario->execute();
        $resultCheckSuperusuario = $stmtCheckSuperusuario->get_result();

        if ($resultCheckSuperusuario->num_rows > 0) {
            $rowConsulta = $resultCheckSuperusuario->fetch_assoc();
            $nombreSuperusuarioAsignado = $rowConsulta['nombre_superusuario'];

            // Si no hay superusuario asignado, asignar el actual superusuario a la consulta
            if (is_null($nombreSuperusuarioAsignado)) {
                $sqlUpdateConsulta = "UPDATE consultas SET nombre_superusuario = ? WHERE id = ?";
                $stmtUpdateConsulta = $conexion->prepare($sqlUpdateConsulta);
                $stmtUpdateConsulta->bind_param("si", $nombreUsuario, $idConsulta);
                if ($stmtUpdateConsulta->execute()) {
                    echo "Superusuario asignado correctamente.";
                } else {
                    echo "Error al asignar el superusuario: " . $stmtUpdateConsulta->error;
                }
                $stmtUpdateConsulta->close();
            } else {
                echo "Superusuario ya asignado a la consulta.";
            }
        } else {
            echo "Consulta no encontrada.";
        }
        $stmtCheckSuperusuario->close();
    }

    header("Location: chat.php?id_consulta=$idConsulta");
} else {
    echo "Error al enviar el mensaje: " . $stmtInsert->error;
}

$stmtInsert->close();
$conexion->close();