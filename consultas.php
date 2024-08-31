<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'];
$emailUsuario = $_SESSION['email'];

// Obtener el ID del usuario de la sesión
$idUsuario = $_SESSION['id_usuario'];

$sql = "SELECT id FROM usuario WHERE email = '$emailUsuario'";
$result = $conexion->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUsuario = $row['id'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Comprobar si es Superusuario
$sqlEsAdmin = "SELECT * FROM superusuario WHERE id_usuario = ? AND (Admin_Global != 0 OR Admin_Contenido != 0 OR Trabajador_social != 0)";
$stmtEsAdmin = $conexion->prepare($sqlEsAdmin);
$stmtEsAdmin->bind_param("i", $idUsuario);
$stmtEsAdmin->execute();
$resultEsAdmin = $stmtEsAdmin->get_result();
$isAdmin = $resultEsAdmin->num_rows > 0;

// Obtener las consultas del usuario
$sqlConsultas = "SELECT * FROM consultas WHERE id_usuario = '$idUsuario' ORDER BY fecha";
$resultConsultas = $conexion->query($sqlConsultas);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <title>Consultas del Usuario</title>
</head>
<body>
<?php include 'menuDash.php'; ?>
<main class="mainDashboard">
    <div class="row">
        <div class="col-md-10 rightColumn">
            <?php if ($isAdmin):?>
                <section>
                    <h3 class="mb-4">Consultas</h3>
                    <div class="mb-3">
                        <input type="text" id="searchQuery" class="form-control" placeholder="Buscar en consultas...">
                    </div>
                    <div id="consultasContainer">
                        <!-- Aquí se cargarán las consultas mediante AJAX -->
                    </div>
                </section>
            <?php else: ?>
                <h3 class="mb-4">Consultas</h3>
                <?php if ($resultConsultas->num_rows > 0): ?>
                    <?php while ($consulta = $resultConsultas->fetch_assoc()): ?>
                        <a href="chat.php?id_consulta=<?= htmlspecialchars($consulta['ID']) ?>" class="text-decoration-none text-black">
                        <div class="<?= $consulta['resuelta'] ? 'custom-card-consultPage-solved' : 'custom-card-consultPage-unsolved' ?>">
                                <h3><?= htmlspecialchars($consulta['Titulo']) ?></h3>
                                <p><?= htmlspecialchars($consulta['Mensaje']) ?></p>
                            </div>
                        </a>                             
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No has realizado ninguna consulta.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
