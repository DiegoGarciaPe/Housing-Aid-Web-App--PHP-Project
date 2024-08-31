<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (!isset($_SESSION['id_usuario'])) {
    echo "Error: Usuario no autenticado.";
    exit;
}

$user_id = $_SESSION['id_usuario'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['Nombre'];
    $apellidos = $_POST['Apellidos'];
    $direccion = $_POST['Direccion'];
    $estadoCivil = $_POST['Estado_Civil'];
    $fechaNacimiento = $_POST['Fecha_Nacimiento'];
    $sexo = $_POST['Sexo'];
    $nacionalidad = $_POST['Nacionalidad'];
    $email = $_POST['Email'];
    $profesion = $_POST['Profesion'];
    $discapacidad = $_POST['Discapacidad'];
    $parado = isset($_POST['Parado']) ? 1 : 0;
    $prestacionDesempleo = isset($_POST['Prestacion_Desempleo']) ? $_POST['Prestacion_Desempleo'] : 0;
    $subsidioDesempleo = isset($_POST['Subsidio_Desempleo']) ? $_POST['Subsidio_Desempleo'] : 0;
    $paroAgotado = isset($_POST['Paro_Cobrado_Agotado']) ? $_POST['Paro_Cobrado_Agotado'] : 0;
    $subsidioAgotado = isset($_POST['Subsidio_Cobrado_Agotado']) ? $_POST['Subsidio_Cobrado_Agotado'] : 0;
    $derechoParo = isset($_POST['Derecho_Paro']) ? $_POST['Derecho_Paro'] : 0;
    $derechoSubsidio = isset($_POST['Derecho_Subsidio']) ? $_POST['Derecho_Subsidio'] : 0;

    // Para depuración
    error_log("Nombre: $nombre");
    error_log("Apellidos: $apellidos");
    error_log("Direccion: $direccion");
    error_log("Estado Civil: $estadoCivil");
    error_log("Fecha Nacimiento: $fechaNacimiento");
    error_log("Sexo: $sexo");
    error_log("Nacionalidad: $nacionalidad");
    error_log("Email: $email");
    error_log("Profesion: $profesion");
    error_log("Discapacidad: $discapacidad");
    error_log("Parado: $parado");
    error_log("Prestacion Desempleo: $prestacionDesempleo");
    error_log("Subsidio Desempleo: $subsidioDesempleo");
    error_log("Paro Cobrado Agotado: $paroAgotado");
    error_log("Subsidio Cobrado Agotado: $subsidioAgotado");
    error_log("Derecho Paro: $derechoParo");
    error_log("Derecho Subsidio: $derechoSubsidio");

    $sql = "UPDATE usuario SET 
            Nombre='$nombre',
            Apellidos='$apellidos',
            Direccion='$direccion',
            Estado_Civil='$estadoCivil',
            Fecha_Nacimiento='$fechaNacimiento',
            Sexo='$sexo',
            Nacionalidad='$nacionalidad',
            Email='$email',
            Profesion='$profesion',
            Discapacidad='$discapacidad',
            Parado='$parado',
            Prestacion_Desempleo='$prestacionDesempleo',
            Subsidio_Desempleo='$subsidioDesempleo',
            Paro_Cobrado_Agotado='$paroAgotado',
            Subsidio_Cobrado_Agotado='$subsidioAgotado',
            Derecho_Paro='$derechoParo',
            Derecho_Subsidio='$derechoSubsidio'
            WHERE id=$user_id";

    // Para depuración
    error_log("SQL: $sql");

    if ($conexion->query($sql) === TRUE) {
        echo "Registro actualizado correctamente";
    } else {
        echo "Error al actualizar el registro: " . $conexion->error;
    }

    $conexion->close();
}
?>
