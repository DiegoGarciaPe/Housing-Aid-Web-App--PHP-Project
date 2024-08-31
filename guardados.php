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
$idUsuario = $_SESSION['id_usuario'];

// Obtener los favoritos del usuario
$sqlFavoritos = "
SELECT a.id AS ayuda_id, a.nombre AS ayuda_nombre, a.descripcion, a.requisitos, a.url AS ayuda_url,
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <title>Ayudas Guardadas</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"/>
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
        .form-check {
            margin-left: 10px;
        }
        .form-check-label {
            margin-left: 5px;
        }
        .contenido, .ayudaTitulo {
            text-align: left;
        }
    </style>
</head>
<body>
<?php include 'menuDash.php'; ?>
<main class="mainDashboard">
    <div class="row">
        <div class="col-md-10 rightColumn">
            <h2>Ayudas Guardadas</h2>
            <?php if ($resultFavoritos->num_rows > 0): ?>
                <div id="tarjetas-container">
                    <?php while ($favorito = $resultFavoritos->fetch_assoc()): ?>
                        <div class="tarjeta" id="ayuda-<?= $favorito['ayuda_id'] ?>">
                            <a href="<?= htmlspecialchars($favorito['ayuda_url']) ?>" class="text-decoration-none text-black">
                                <h3 class="ayudaTitulo"><?= htmlspecialchars($favorito['ayuda_nombre']) ?></h3>
                                <div class="contenido">
                                    <p class="ayudaDesc">Descripción: <?= htmlspecialchars($favorito['descripcion']) ?></p>
                                    <p class="ayudaReq">Requisitos: <?= htmlspecialchars($favorito['requisitos']) ?></p>
                                </div>
                                <div class="categoria-imagen">
                                    <?php
                                    $imagenes = explode(',', $favorito['categorias_imagen']);
                                    foreach ($imagenes as $imagen) {
                                        echo "<img src='".htmlspecialchars($imagen)."' alt='Categoria'>";
                                    }
                                    ?>
                                </div>
                            </a>
                            <i class="fa-solid fa-bookmark favorito-icono" title="Eliminar de Ayudas Guardadas" data-ayuda-id="<?= $favorito['ayuda_id'] ?>" data-action="remove"></i>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No tienes ayudas guardadas como favoritas.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Añadir evento de click a los iconos de guardados
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
                // Eliminar la tarjeta del DOM
                document.getElementById('ayuda-' + ayudaId).remove();
            }
            Swal.fire({
                icon: 'success',
                title: action === 'add' ? 'Guardado' : 'Eliminado de Ayudas Guardadas',
                showConfirmButton: false,
                timer: 1500
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
