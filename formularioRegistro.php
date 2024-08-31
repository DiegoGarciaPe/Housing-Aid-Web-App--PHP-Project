<form method="POST" action="#registro-section">
    <h2>Regístrate</h2>
    <div class="input-group">
        <input type="text" name="nombre" required>
        <label>Nombre</label>
    </div>
    <div class="input-group">
        <input type="text" name="email" required>
        <label>Email</label>
    </div>
    <div class="input-group" style="position: relative;">
        <input type="password" name="password" required id="register-password">
        <label>Contraseña</label>
        <button type="button" id="toggleRegisterPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <div class="input-group" style="position: relative;">
        <input type="password" name="repite_password" required id="confirm-password">
        <label>Repite Contraseña</label>
        <button type="button" id="toggleConfirmPassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <div class="rebember">
        <input type="checkbox" name="terms" required>
        <label>Acepto los términos y condiciones</label>
    </div>
    <button type="submit" class="formBtn" name="register">Regístrate</button>
    <div class="signUp-link">
     <p>¿Ya tienes cuenta? <a href="#" class="signInBtn-link">Inicia sesión</a></p>
    </div>
</form>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    include 'conexionBBDD.php';
    $conexion = conexionBBDD();

    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $repite_password = $_POST["repite_password"];

    if ($password !== $repite_password) {
        echo "<script>Swal.fire({
            title: 'Error',
            text: 'Las contraseñas no coinciden.',
            icon: 'error'
        }).then(function() {
            window.location = '#registro-section';
        });</script>";
        exit;
    }

    $patron = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.])[A-Za-z\d@$!%*?&.]{8,}$/";
    if (!preg_match($patron, $password)) {
        echo "<script>Swal.fire({
            title: 'Error',
            text: 'La contraseña debe tener al menos 8 caracteres, incluir una mayúscula, una minúscula, un número y un símbolo especial.',
            icon: 'error'
        }).then(function() {
            window.location = '#registro-section';
        });</script>";
        exit;
    }

    $consultaEmail = "SELECT Email FROM usuario WHERE Email = ?";
    $stmtEmail = mysqli_prepare($conexion, $consultaEmail);
    mysqli_stmt_bind_param($stmtEmail, "s", $email);
    mysqli_stmt_execute($stmtEmail);
    mysqli_stmt_store_result($stmtEmail);

    if (mysqli_stmt_num_rows($stmtEmail) > 0) {
        echo "<script>Swal.fire({
            title: 'Error',
            text: 'El email ya está registrado.',
            icon: 'error'
        }).then(function() {
            window.location = '#registro-section';
        });</script>";
        mysqli_stmt_close($stmtEmail);
        cerrarConexion($conexion);
        exit;
    }
    mysqli_stmt_close($stmtEmail);

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $consulta = "INSERT INTO usuario (Nombre, Email, Password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "sss", $nombre, $email, $password_hash);

    if (mysqli_stmt_execute($stmt)) {
        $id_usuario = mysqli_insert_id($conexion);

        $consultaSuperUsuario = "INSERT INTO superusuario (id_usuario, admin_global, admin_contenido, trabajador_social) VALUES (?, 0, 0, 0)";
        $stmtSuperUsuario = mysqli_prepare($conexion, $consultaSuperUsuario);
        mysqli_stmt_bind_param($stmtSuperUsuario, "i", $id_usuario);

        if (mysqli_stmt_execute($stmtSuperUsuario)) {
            // Iniciar sesión automáticamente después de registrarse
            session_start();
            $_SESSION["id_usuario"] = $id_usuario;
            $_SESSION["email"] = $email;
            $_SESSION["nombre"] = $nombre;

            echo "<script>Swal.fire({
                title: '¡Genial!',
                text: 'Usuario registrado correctamente.',
                icon: 'success'
            }).then(function() {
                window.location = 'dashboardUsuario.php';
            });</script>";
        } else {
            $error = mysqli_error($conexion);
            echo "<script>Swal.fire({
                title: 'Error',
                text: 'Error al registrar el superusuario: $error',
                icon: 'error'
            }).then(function() {
                window.location = '#registro-section';
            });</script>";
        }

        mysqli_stmt_close($stmtSuperUsuario);
    } else {
        $error = mysqli_error($conexion);
        echo "<script>Swal.fire({
            title: 'Error',
            text: 'Error al registrar el usuario: $error',
            icon: 'error'
        }).then(function() {
            window.location = '#registro-section';
        });</script>";
    }

    mysqli_stmt_close($stmt);
    cerrarConexion($conexion);
}
?>
