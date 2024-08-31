<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $roles = ['admin_global', 'admin_contenido', 'trabajador_social'];
    $userRoles = [];

    // Inicializar todos los roles a 0
    foreach ($_POST['user_id'] as $userId) {
        $userRoles[$userId] = [
            'admin_global' => 0,
            'admin_contenido' => 0,
            'trabajador_social' => 0
        ];
    }

    // Asignar roles seleccionados
    foreach ($roles as $role) {
        if (isset($_POST[$role])) {
            foreach ($_POST[$role] as $userId) {
                $userRoles[$userId][$role] = 1;
            }
        }
    }

    // Actualizar roles en la base de datos
    foreach ($userRoles as $userId => $roles) {
        // Verificar si el usuario ya tiene registros en la tabla superusuario
        $sqlCheck = "SELECT COUNT(*) as count FROM superusuario WHERE id_usuario = ?";
        $stmtCheck = $conexion->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $userId);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        $rowCheck = $resultCheck->fetch_assoc();
        $stmtCheck->close();

        if ($rowCheck['count'] > 0) {
            // Si ya existe, actualizar los roles
            $sqlUpdate = "UPDATE superusuario SET admin_global = ?, admin_contenido = ?, trabajador_social = ? WHERE id_usuario = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->bind_param("iiii", $roles['admin_global'], $roles['admin_contenido'], $roles['trabajador_social'], $userId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            // Si no existe, insertar un nuevo registro
            $sqlInsert = "INSERT INTO superusuario (id_usuario, admin_global, admin_contenido, trabajador_social) VALUES (?, ?, ?, ?)";
            $stmtInsert = $conexion->prepare($sqlInsert);
            $stmtInsert->bind_param("iiii", $userId, $roles['admin_global'], $roles['admin_contenido'], $roles['trabajador_social']);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
    }

    $conexion->close();
    header("Location: administrarUsuarios.php");
    exit();
}