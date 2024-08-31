<div class="container contact-form">
    <h2 class="contact">¿En qué podemos ayudarte?</h2>
    <form method="POST" class="custom-form">
        <div class="input-group contact">
            <input type="text" name="nombre" required>
            <label>Nombre</label>
        </div>
        <div class="input-group contact">
            <input type="text" name="email" required>
            <label>Email</label>
        </div>
        <div class="input-group contact">
            <input type="text" name="asunto" required>
            <label>Asunto</label>
        </div>
        <div class="input-group contact">
            <textarea name="mensaje" class="mensajeContacto" required></textarea>
            <label>Mensaje</label>
        </div>
        <button class="contactBtn" type="submit" name="submit">Enviar Mensaje</button>
    </form>
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        include 'conexionBBDD.php';
        $conexion = conexionBBDD();

        // Tomar los valores del formulario
        $email = $_POST['email'];
        $asunto = $_POST['asunto'];
        $mensaje = $_POST['mensaje'];

        // Preparar la consulta para buscar el ID del usuario por email
        $stmt = $conexion->prepare("SELECT ID FROM usuario WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id_usuario = $row['ID'];

            // Insertar los datos en la tabla 'consultas'
            $stmt = $conexion->prepare("INSERT INTO consultas (Id_usuario, Titulo, Mensaje, resuelta, fecha) VALUES (?, ?, ?, 0, NOW())");
            $stmt->bind_param("iss", $id_usuario, $asunto, $mensaje);
            $stmt->execute();
            echo "<script>Swal.fire('¡Genial!', 'Mensaje enviado correctamente.', 'success');</script>";
        } else {
            echo "<script>Swal.fire('Error', 'El email no está registrado.', 'error');</script>";
        }

        // Cerrar la conexión
        $stmt->close();
        cerrarConexion($conexion);
    }
    ?>
</div>
