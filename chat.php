<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

require 'conexionBBDD.php';
$conexion = conexionBBDD();

$emailUsuario = $_SESSION["email"];
$idConsulta = $_GET['id_consulta'];

$sql = "SELECT id FROM usuario WHERE email = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("s", $emailUsuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUsuario = $row['id'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Obtener el título y mensaje original de la consulta
$sqlConsulta = "SELECT Titulo, Mensaje FROM consultas WHERE ID = ?";
$stmtConsulta = $conexion->prepare($sqlConsulta);
$stmtConsulta->bind_param("i", $idConsulta);
$stmtConsulta->execute();
$resultConsulta = $stmtConsulta->get_result();

if ($resultConsulta->num_rows > 0) {
    $rowConsulta = $resultConsulta->fetch_assoc();
    $tituloConsulta = $rowConsulta['Titulo'];
    $mensajeOriginal = $rowConsulta['Mensaje'];
} else {
    echo "Error: Consulta no encontrada.";
    exit();
}

// Obtener el usuario del chat
$sqlUsuario = "SELECT Nombre FROM usuario WHERE email = ?";
$stmtUsuario = $conexion->prepare($sqlUsuario);
$stmtUsuario->bind_param("s", $emailUsuario);
$stmtUsuario->execute();
$resultUsuario = $stmtUsuario->get_result();

if ($resultUsuario->num_rows > 0) {
    $rowUsuario = $resultUsuario->fetch_assoc();
    $nombreUsuario = $rowUsuario['Nombre'];
} else {
    echo "Error: Consulta no encontrada.";
    exit();
}

// Obtener los mensajes del chat
$sqlMensajes = "SELECT * FROM mensajes WHERE id_consulta = ? ORDER BY fecha ASC";
$stmtMensajes = $conexion->prepare($sqlMensajes);
$stmtMensajes->bind_param("i", $idConsulta);
$stmtMensajes->execute();
$resultMensajes = $stmtMensajes->get_result();

// Comprobar si es Superusuario
$sqlEsAdmin = "SELECT * FROM superusuario WHERE id_usuario = ? AND (Admin_Global != 0 OR Admin_Contenido != 0 OR Trabajador_social != 0)";
$stmtEsAdmin = $conexion->prepare($sqlEsAdmin);
$stmtEsAdmin->bind_param("i", $idUsuario);
$stmtEsAdmin->execute();
$resultEsAdmin = $stmtEsAdmin->get_result();
$isAdmin = $resultEsAdmin->num_rows > 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <title>Chat con Superusuario</title>
</head>
<body>
<?php include 'menuDash.php'; ?>
<main class="mainDashboard">
    <div class="row">
        <div class="col-md-10">
            <section>
                <h3 class="h3Dashboard mt-3">Chat de Consulta: <?= htmlspecialchars($tituloConsulta) ?></h3>
                <a href="consultas.php">
                    <button class="btn btn-outline-success btn-sm my-3">Volver a Consultas</button>
                </a>
                <div id="chat-container" class="chat-container">
                    <div class="original-message">
                        <p><strong>Consulta Original:</strong> <?= htmlspecialchars($mensajeOriginal) ?></p>
                    </div>
                    <?php if ($resultMensajes->num_rows > 0): ?>
                        <?php while ($mensaje = $resultMensajes->fetch_assoc()): ?>
                            <div class="chat-message <?= $mensaje['es_superusuario'] ? 'admin-message' : 'user-message' ?>">
                                <p><strong><?= $mensaje['es_superusuario'] ? 'Administrador' : $nombreUsuario ?>:</strong> <?= htmlspecialchars($mensaje['mensaje']) ?></p>
                                <span class="message-date"><?= date("d/m/Y H:i", strtotime($mensaje['fecha'])) ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No hay mensajes en este chat.</p>
                    <?php endif; ?>
                </div>
                <form action="enviarMensaje.php" method="post">
                    <input type="hidden" name="id_consulta" value="<?= $idConsulta ?>">
                    <input type="hidden" name="es_superusuario" value="<?= $isAdmin ? 1 : 0 ?>">
                    <div class="mb-3">
                        <textarea name="mensaje" class="form-control sendForm" rows="3" placeholder="Escribe tu mensaje..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-outline-success">Enviar</button>
                </form>
            </section>
        </div>
    </div>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chatContainer = document.getElementById("chat-container");
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
