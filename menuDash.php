<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["email"])) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION["id_usuario"])) {
    die("Error: El ID del usuario no está establecido en la sesión. Verifica el proceso de inicio de sesión.");
}

$nombreUsuario = $_SESSION["nombre"];
$idUsuario = $_SESSION["id_usuario"];

require_once 'conexionBBDD.php';

try {
    $conexion = conexionBBDD();

    $stmt = $conexion->prepare('SELECT Admin_Global, Admin_Contenido, Trabajador_Social FROM superusuario WHERE id_usuario = ?');
    $stmt->bind_param('i', $idUsuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $roles = $result->fetch_assoc();

    if (!$roles) {
        die("Error: No se encontraron roles para el usuario.");
    }

    cerrarConexion($conexion);
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}

$adminGlobal = $roles['Admin_Global'];
$adminContenido = $roles['Admin_Contenido'];
$trabajadorSocial = $roles['Trabajador_Social'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
    <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
            rel="stylesheet"
    />
    <link rel="stylesheet" href="style.css" />
    <link
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            rel="stylesheet"
    />
    <title>Sidebar Dashboard Menu</title>
</head>
<body class="body_sidebar">
<aside class="aside active" id="aside">
    <div class="head">
        <div class="profile">
            <img src="images/beanhead.png">
            <span><?= htmlspecialchars($nombreUsuario) ?></span>
        </div>

        <i class="fa-solid fa-bars" id="menu"></i>
    </div>
    <div class="options">
        <div onclick="location.href='dashboardUsuario.php'" title="Vista General">
            <i class="fa-solid fa-table-columns"></i>
            <span class="option">Vista General</span>
        </div>
        <div onclick="location.href='miPerfil.php'" title="Mi Perfil">
            <i class="fa-solid fa-user"></i>
            <span class="option">Mi Perfil</span>
        </div>
        <div onclick="location.href='consultas.php'" title="Consultas">
            <i class="fa-solid fa-envelope"></i>
            <span class="option">Consultas</span>
        </div>
        <div title="Ayudas Guardadas" onclick="location.href='guardados.php'">
            <i class="fa-solid fa-bookmark"></i>
            <span class="option">Ayudas Guardadas</span>
        </div>
        <?php if ($adminGlobal): ?>
            <div onclick="location.href='administrarUsuarios.php'" title="Administrar usuarios">
                <i class="fa-solid fa-users"></i>
                <span class="option">Administrar Usuarios</span>
            </div>
            <div title="Moderar Enlaces de Confianza" onclick="location.href='moderarEnlaces.php'">
                <i class="fa-solid fa-pen"></i>
                <span class="option">Moderar Enlaces</span>
            </div>
            <div title="Moderar Ayudas" onclick="location.href='moderarAyudas.php'">
                <i class="fa-solid fa-life-ring"></i>
                <span class="option">Moderar Ayudas</span>
            </div>
            <div title="Usuarios">
                <i class="fa-solid fa-user-group" onclick="location.href='usuarios.php'"></i>
                <span class="option">Usuarios</span>
            </div>

        <?php endif; ?>
        <?php if ($adminContenido && !$adminGlobal): ?>
            <div title="Moderar Enlaces de Confianza" onclick="location.href='moderarEnlaces.php'">
                <i class="fa-solid fa-pen"></i>
                <span class="option">Moderar Enlaces de Confianza</span>
            </div>
            <div title="Moderar Ayudas" onclick="location.href='moderarAyudas.php'">
                <i class="fa-solid fa-life-ring"></i>
                <span class="option">Moderar Ayudas</span>
            </div>
            <div title="Usuarios" onclick="location.href='usuarios.php'">
                <i class="fa-solid fa-user-group"></i>
                <span class="option">Usuarios</span>
            </div>

        <?php endif; ?>
        <?php if ($trabajadorSocial && !$adminGlobal): ?>
            <div title="Usuarios" onclick="location.href='usuarios.php'">
                <i class="fa-solid fa-user-group"></i>
                <span class="option">Usuarios</span>
            </div>
        <?php endif; ?>
        <div onclick="location.href='index.php'" title="Volver al inicio">
            <i class="fa-solid fa-home"></i>
            <span class="option">Volver al inicio</span>
        </div>
        <div id="logout" title="Cerrar Sesión">
            <i class="fa-solid fa-sign-out"></i>
            <span class="option">Cerrar Sesión</span>
        </div>
    </div>
</aside>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.getElementById('logout').addEventListener('click', function() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Estás a punto de cerrar sesión!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, cerrar sesión'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php';
            }
        })
    });
</script>
<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>



