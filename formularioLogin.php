<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$email = "";
if (isset($_COOKIE['remembered_email'])) {
    $email = $_COOKIE['remembered_email'];
}
?>

<form method="POST" action="">
    <h2>Login</h2>
    <div class="input-group" style="position: relative;">
        <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <label for="">Email</label>
    </div>
    <div class="input-group" style="position: relative;">
        <input type="password" name="password" required id="password">
        <label for="">Contraseña</label>
        <button type="button" id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none;">
            <i class="fas fa-eye"></i>
        </button>
    </div>
    <div class="remember">
        <label><input type="checkbox" name="remember" <?php if (isset($_COOKIE['remembered_email'])) echo 'checked'; ?>> Recordar usuario</label>
    </div>
    <button class="formBtn" type="submit" name="login">Iniciar sesión</button>
    <div class="signUp-link">
        <p>¿No tienes cuenta? <a href="#" class="signUpBtn-link">Regístrate</a></p>
    </div>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    include 'conexionBBDD.php';
    $conexion = conexionBBDD();

    $email = $_POST["email"];
    $password_introducida = $_POST["password"];

    $sql = "SELECT id, nombre, password FROM usuario WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_usuario = $row["id"];
        $usuario = $row["nombre"];
        $password_hash = $row['password'];

        if (password_verify($password_introducida, $password_hash)) {
            $_SESSION["id_usuario"] = $id_usuario; // Almacenar el ID del usuario en la sesión
            $_SESSION["email"] = $email;
            $_SESSION["nombre"] = $usuario;

            // Verificar que el ID del usuario se almacena en la sesión
            if (!isset($_SESSION["id_usuario"])) {
                die("Error: No se pudo establecer el ID del usuario en la sesión.");
            }

            // Set cookie if "Remember user" is checked
            if (isset($_POST['remember'])) {
                setcookie('remembered_email', $email, time() + (86400 * 30), "/"); // 30 días
            } else {
                setcookie('remembered_email', '', time() - 3600, "/"); // borrar cookie
            }

            echo "<script>
                Swal.fire({
                    title: '¡Genial!',
                    text: 'Bienvenido, $usuario!',
                    icon: 'success'
                }).then(function() {
                    window.location = 'dashboardUsuario.php';
                });
            </script>";
            exit;
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error',
                    text: 'Contraseña incorrecta',
                    icon: 'error'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error',
                text: 'Usuario no encontrado',
                icon: 'error'
            });
        </script>";
    }

    $stmt->close();
    cerrarConexion($conexion);
}
?>
