<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

// Verificar si el usuario ha iniciado sesión
$usuarioLogueado = isset($_SESSION['email']);
$nombreUsuario = $usuarioLogueado ? $_SESSION['nombre'] : '';
$emailUsuario = $usuarioLogueado ? $_SESSION['email'] : '';
$idUsuario = $usuarioLogueado ? $_SESSION['id_usuario'] : 0;

// Obtener las categorías disponibles
$sqlCategorias = "SELECT id, nombre FROM categorias";
$resultCategorias = $conexion->query($sqlCategorias);
$categorias = [];
if ($resultCategorias->num_rows > 0) {
    while ($categoria = $resultCategorias->fetch_assoc()) {
        $categorias[] = $categoria;
    }
}

// Obtener las ayudas ordenadas por fecha de creación y filtradas por categorías y búsqueda si es necesario
$categoriasFiltradas = isset($_GET['categorias']) ? $_GET['categorias'] : [];
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';
$sqlAyudas = "
SELECT a.id AS ayuda_id, a.nombre AS ayuda_nombre, a.descripcion, a.requisitos, a.url AS ayuda_url,
       GROUP_CONCAT(c.nombre SEPARATOR ', ') AS categorias, GROUP_CONCAT(c.url SEPARATOR ', ') AS categorias_imagen
FROM ayuda a
JOIN ayuda_categoria ac ON a.id = ac.ayuda_id
JOIN categorias c ON ac.categoria_id = c.id";

$clauses = [];
if (!empty($categoriasFiltradas)) {
    $categoriasFiltradas = array_map('intval', $categoriasFiltradas);
    $clauses[] = "(SELECT COUNT(*) FROM ayuda_categoria WHERE ayuda_id = a.id AND categoria_id IN (" . implode(',', $categoriasFiltradas) . ")) = " . count($categoriasFiltradas);
}

if (!empty($busqueda)) {
    $clauses[] = "(a.nombre LIKE '%" . $conexion->real_escape_string($busqueda) . "%' OR a.descripcion LIKE '%" . $conexion->real_escape_string($busqueda) . "%')";
}

if (!empty($clauses)) {
    $sqlAyudas .= " WHERE " . implode(' AND ', $clauses);
}

$sqlAyudas .= " GROUP BY a.id ORDER BY a.fecha_creacion DESC";
$resultAyudas = $conexion->query($sqlAyudas);

// Obtener los favoritos del usuario logueado
$favoritos = [];
if ($usuarioLogueado) {
    $sqlFavoritos = "SELECT id_ayuda FROM favoritos WHERE id_usuario = ?";
    $stmtFavoritos = $conexion->prepare($sqlFavoritos);
    $stmtFavoritos->bind_param('i', $idUsuario);
    $stmtFavoritos->execute();
    $resultFavoritos = $stmtFavoritos->get_result();
    while ($row = $resultFavoritos->fetch_assoc()) {
        $favoritos[] = $row['id_ayuda'];
    }
}

// Manejar solicitud de guardados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorito'])) {
    if ($usuarioLogueado && !empty($_POST['ayuda_id'])) {
        $ayudaId = $_POST['ayuda_id'];

        if ($_POST['action'] === 'add') {
            $sql = "INSERT INTO favoritos (id_usuario, id_ayuda) VALUES (?, ?)";
        } else if ($_POST['action'] === 'remove') {
            $sql = "DELETE FROM favoritos WHERE id_usuario = ? AND id_ayuda = ?";
        }

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ii', $idUsuario, $ayudaId);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
    exit();
}

// Devolver los resultados en formato JSON para AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax'])) {
    $ayudas = [];
    if ($resultAyudas->num_rows > 0) {
        while ($ayuda = $resultAyudas->fetch_assoc()) {
            $ayuda['favorito'] = $usuarioLogueado && in_array($ayuda['ayuda_id'], $favoritos);
            $ayudas[] = $ayuda;
        }
    }
    echo json_encode($ayudas);
    exit();
}
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
    <title>Ayudas</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php include 'header.php' ?>

<main class="ayudasHtml">
    <div class="row">
        <!--Columna filtro-->
        <div class="col-md-3 ms-5">
            <h3 class="mt-4 mb-3 Noticias">Buscar</h3>
            <input type="text" id="buscador" class="form-control buscador-contain" placeholder="Buscar ayudas...">
            <br>
            <h3 class="mb-3 Noticias">Filtrar por Categoría</h3>
            <form id="filtro-form" class="">
                <?php foreach ($categorias as $categoria): ?>
                    <div class="form-check2">
                        <input class="form-check-input categoria-checkbox" type="checkbox" name="categorias[]" value="<?= $categoria['id'] ?>"
                            <?= in_array($categoria['id'], $categoriasFiltradas) ? 'checked' : '' ?>>
                        <label class="form-check-label">
                            <?= htmlspecialchars($categoria['nombre']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </form>
        </div>
        <!--Columna ayudas-->
        <div class="col-md-8">
            <h2 class="mb-4">Ayudas</h2>
            <div id="tarjetas-container">
                <?php if ($resultAyudas->num_rows > 0): ?>
                    <?php while ($ayuda = $resultAyudas->fetch_assoc()): ?>
                        <div class="tarjeta p-3" id="ayuda-<?= $ayuda['ayuda_id'] ?>">
                            <h3 class="ayudaTitulo"><?= htmlspecialchars($ayuda['ayuda_nombre']) ?></h3>
                            <div class="contenido">
                                <p class="ayudaDesc">Descripción: <?= htmlspecialchars($ayuda['descripcion']) ?></p>
                                <p class="ayudaReq">Requisitos: <?= htmlspecialchars($ayuda['requisitos']) ?></p>
                            </div>
                            <div class="categoria-imagen">
                                <?php
                                $imagenes = explode(',', $ayuda['categorias_imagen']);
                                foreach ($imagenes as $imagen) {
                                    echo "<img src='".htmlspecialchars($imagen)."' alt='Categoria'>";
                                }
                                ?>
                            </div>
                            <?php if ($usuarioLogueado): ?>
                                <?php if (in_array($ayuda['ayuda_id'], $favoritos)): ?>
                                    <i class="fa-solid fa-bookmark favorito-icono" title="Eliminar de Guardados" data-ayuda-id="<?= $ayuda['ayuda_id'] ?>" data-action="remove"></i>
                                <?php else: ?>
                                    <i class="fa-regular fa-bookmark favorito-icono" title="Guardar" data-ayuda-id="<?= $ayuda['ayuda_id'] ?>" data-action="add"></i>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay ayudas disponibles con estos filtros.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-1"></div>
    </div>
</main>

<?php include 'footer.php' ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Añadir evento de click a los iconos de favoritos
        document.querySelectorAll(".favorito-icono").forEach(function(icono) {
            icono.addEventListener("click", function(event) {
                event.stopPropagation(); // Evita que el evento de click en el icono cierre el modal o abra el enlace
                const ayudaId = this.getAttribute("data-ayuda-id");
                const action = this.getAttribute("data-action");
                const iconoElement = this;

                $.post('ayudas.php', { favorito: true, ayuda_id: ayudaId, action: action }, function(response) {
                    if (response === 'success') {
                        if (action === 'add') {
                            iconoElement.classList.remove("fa-regular");
                            iconoElement.classList.add("fa-solid");
                            iconoElement.setAttribute("data-action", "remove");
                            iconoElement.setAttribute("title", "Eliminar de Guardados");
                            Swal.fire({
                                icon: 'success',
                                title: '¡Ayuda guardada!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else if (action === 'remove') {
                            iconoElement.classList.remove("fa-solid");
                            iconoElement.classList.add("fa-regular");
                            iconoElement.setAttribute("data-action", "add");
                            iconoElement.setAttribute("title", "Guardar");
                            Swal.fire({
                                icon: 'success',
                                title: 'Ayuda eliminada de Guardados',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'No se pudo actualizar el estado del favorito.',
                        });
                    }
                });
            });
        });

        // Añadir evento de cambio a los checkboxes de categorías
        document.querySelectorAll(".categoria-checkbox").forEach(function(checkbox) {
            checkbox.addEventListener("change", function() {
                actualizarAyudas();
            });
        });

        // Añadir evento de input a la barra de búsqueda
        document.getElementById("buscador").addEventListener("input", function() {
            actualizarAyudas();
        });

        function actualizarAyudas() {
            const checkboxes = document.querySelectorAll(".categoria-checkbox:checked");
            const categorias = Array.from(checkboxes).map(cb => cb.value);
            const search = document.getElementById("buscador").value;

            $.get('ayudas.php', { categorias: categorias, search: search, ajax: true }, function(response) {
                const ayudas = JSON.parse(response);
                const tarjetasContainer = document.getElementById("tarjetas-container");
                tarjetasContainer.innerHTML = '';

                if (ayudas.length > 0) {
                    ayudas.forEach(function(ayuda) {
                        const tarjeta = document.createElement("div");
                        tarjeta.className = "tarjeta p-3";
                        tarjeta.innerHTML = `
                        <h3 class="ayudaTitulo">${ayuda.ayuda_nombre}</h3>
                        <div class="contenido">
                            <p class="ayudaDesc">Descripción: ${ayuda.descripcion}</p>
                            <p class="ayudaReq">Requisitos: ${ayuda.requisitos}</p>
                        </div>
                        <div class="categoria-imagen">
                            ${ayuda.categorias_imagen.split(',').map(img => `<img src="${img}" alt="Categoria">`).join('')}
                        </div>
                        ${<?= $usuarioLogueado ? 'true' : 'false' ?> ? (ayuda.favorito
                                ? `<i class="fa-solid fa-bookmark favorito-icono" title="Eliminar de Guardados" data-ayuda-id="${ayuda.ayuda_id}" data-action="remove"></i>`
                                : `<i class="fa-regular fa-bookmark favorito-icono" title="Guardar" data-ayuda-id="${ayuda.ayuda_id}" data-action="add"></i>`)
                            : ''
                        }
                    `;

                        tarjetasContainer.appendChild(tarjeta);
                    });
                } else {
                    tarjetasContainer.innerHTML = '<p>No hay ayudas disponibles con estos filtros.</p>';
                }

                // Reaplicar eventos a los nuevos iconos de favoritos
                document.querySelectorAll(".favorito-icono").forEach(function(icono) {
                    icono.addEventListener("click", function(event) {
                        event.stopPropagation();
                        const ayudaId = this.getAttribute("data-ayuda-id");
                        const action = this.getAttribute("data-action");
                        const iconoElement = this;

                        $.post('ayudas.php', { favorito: true, ayuda_id: ayudaId, action: action }, function(response) {
                            if (response === 'success') {
                                if (action === 'add') {
                                    iconoElement.classList.remove("fa-regular");
                                    iconoElement.classList.add("fa-solid");
                                    iconoElement.setAttribute("data-action", "remove");
                                    iconoElement.setAttribute("title", "Eliminar de Guardados");
                                    Swal.fire({
                                        icon: 'success',
                                        title: '¡Ayuda guardada!',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                } else if (action === 'remove') {
                                    iconoElement.classList.remove("fa-solid");
                                    iconoElement.classList.add("fa-regular");
                                    iconoElement.setAttribute("data-action", "add");
                                    iconoElement.setAttribute("title", "Guardar");
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Ayuda eliminada de Guardados',
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'No se pudo actualizar el estado del favorito.',
                                });
                            }
                        });
                    });
                });
            });
        }
    });
</script>
</body>
</html>
