<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

//Obtener todas las consultas
$sqlTodasConsultas = "SELECT * FROM consultas ORDER BY fecha DESC LIMIT 5";
$resultTodasConsultas = $conexion->query($sqlTodasConsultas);

// Obtener las consultas del usuario
$sqlConsultas = "SELECT * FROM consultas WHERE id_usuario = '$idUsuario' ORDER BY fecha DESC LIMIT 5";
$resultConsultas = $conexion->query($sqlConsultas);

// Obtener los favoritos del usuario
$sqlFavoritos = "

SELECT a.id AS ayuda_id, a.nombre AS ayuda_nombre, a.url AS ayuda_url,

       GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias, GROUP_CONCAT(c.url SEPARATOR ', ') AS categorias_imagen
FROM favoritos f
JOIN ayuda a ON f.id_ayuda = a.id
JOIN ayuda_categoria ac ON a.id = ac.ayuda_id
JOIN categorias c ON ac.categoria_id = c.id
WHERE f.id_usuario = ?
GROUP BY a.id
ORDER BY a.fecha_creacion DESC";
$stmtFavoritos = $conexion->prepare($sqlFavoritos);
$stmtFavoritos->bind_param('i', $idUsuario);
$stmtFavoritos->execute();
$resultFavoritos = $stmtFavoritos->get_result();


//Obtener las últimas ayudas añadidas
$sqlLast = "SELECT Nombre, Url FROM ayuda ORDER BY fecha_creacion DESC LIMIT 4";
$resultLast = $conexion->query($sqlLast);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <title>Dashboard Usuario</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .tarjeta {
            position: relative;
        }

        .favorito-icono {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .contenido,
        .ayudaTitulo {
            text-align: left;
        }
    </style>
</head>

<body>
    <?php require 'menuDash.php'; ?>
    <main class="mainDashboard">
        <div class="row">
            <div class="col-md-10 rightColumn">
                <section>
                    <h3 class="h3Dashboard">¡Hola, <?= htmlspecialchars($nombreUsuario) ?>!</h3>
                    <p>Bienvenido a tu Dashboard</p>
                </section>
                <hr class="hrInvisible" />
                <!-- Seccion de Consultas -->
                <section>
                    <?php if ($isAdmin) : ?>
                        <h3 class="h3Dashboard">Consultas de los usuarios</h3>
                        <div class="custom-card-container">
                            <?php while ($consulta = $resultTodasConsultas->fetch_assoc()) : ?>
                                <a href="chat.php?id_consulta=<?= htmlspecialchars($consulta['ID']) ?>" class="text-decoration-none text-black">
                                    <div class="custom-card">
                                        <h2 class="ayudaTitulo">
                                            <?= htmlspecialchars(mb_strimwidth($consulta['Titulo'], 0, 50, '...')) ?>
                                        </h2>
                                        <p>
                                            <?= htmlspecialchars(mb_strimwidth($consulta['Mensaje'], 0, 50, '...')) ?>
                                        </p>
                                    </div>
                                </a>
                            <?php endwhile; ?>

                        </div>
                    <?php else : ?>
                        <h3 class="h3Dashboard">Consultas</h3>
                        <div class="custom-card-container">
                            <?php if ($resultConsultas->num_rows > 0) : ?>
                                <?php while ($consulta = $resultConsultas->fetch_assoc()) : ?>
                                    <a href="chat.php?id_consulta=<?= htmlspecialchars($consulta['ID']) ?>" class="text-decoration-none text-black">
                                        <div class="custom-card">
                                            <h2 class="ayudaTitulo"><?= htmlspecialchars(mb_strimwidth($consulta['Titulo'], 0, 50, '...')) ?></h2>
                                            <p><?= htmlspecialchars(mb_strimwidth($consulta['Mensaje'], 0, 50, '...')) ?></p>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <p>No has realizado ninguna consulta.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </section>
                <hr class="hrInvisible" />
                <!-- Ayudas Guardadas -->
                <section>
                    <h3 class="h3Dashboard">Ayudas Guardadas</h3>
                    <?php if ($resultFavoritos->num_rows > 0) : ?>
                        <div class="custom-card-container">
                            <?php while ($favorito = $resultFavoritos->fetch_assoc()) : ?>
                                <a href="<?= htmlspecialchars($favorito['ayuda_url']) ?>" target="_blank" class="custom-card-2 tarjeta custom-card-link">
                                    <h2 class="ayudaTitulo"><?= htmlspecialchars(mb_strimwidth($favorito['ayuda_nombre'], 0, 92, '...')) ?></h2>
                                    <i class="fa-solid fa-bookmark favorito-icono" title="Eliminar de Ayudas Guardadas" data-ayuda-id="<?= $favorito['ayuda_id'] ?>" data-action="remove"></i>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    <?php else : ?>
                        <p>No tienes ayudas guardadas.</p>

                    <?php endif; ?>
                </section>
                <hr class="hrInvisible" />
                <!-- Ayudas Recientes -->
                <section>
                    <h3 class="h3Dashboard">Ayudas Recientes</h3>
                    <div class="custom-card-container">
                        <?php if ($resultLast->num_rows > 0) : ?>
                            <?php while ($lastHelp = $resultLast->fetch_assoc()) : ?>
                                <a href="<?= htmlspecialchars($lastHelp['Url']); ?>" target="_blank" class="custom-card-link">
                                    <div class="custom-card-3">
                                        <h2><?= htmlspecialchars(mb_strimwidth($lastHelp['Nombre'], 0, 92, '...')); ?></h2>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <p>No hay ninguna ayuda disponible.</p>
                        <?php endif; ?>
                    </div>
                </section>
                <hr class="hrInvisible" />
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Añadir evento de click a los iconos de favoritos
            document.querySelectorAll('.favorito-icono').forEach(function(icono) {
                icono.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const action = this.getAttribute('data-action');
                    gestionarFavorito(this.dataset.ayudaId, action, this);
                });
            });
        });


        function gestionarFavorito(ayudaId, action, icono) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "ayudas.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status === 200 && xhr.responseText === 'success') {
                    if (action === 'add') {
                        icono.classList.remove('fa-regular');
                        icono.classList.add('fa-solid');
                        icono.setAttribute('data-action', 'remove');
                    } else {
                        icono.classList.remove('fa-solid');
                        icono.classList.add('fa-regular');
                        icono.setAttribute('data-action', 'add');
                    }
                    Swal.fire({
                        icon: 'success',
                        title: action === 'add' ? 'Guardado' : 'Eliminado de Ayudas Guardadas',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo completar la acción.'
                    });
                }
            };
            xhr.send("favorito=true&ayuda_id=" + ayudaId + "&action=" + action);
        }
    </script>
</body>

</html>