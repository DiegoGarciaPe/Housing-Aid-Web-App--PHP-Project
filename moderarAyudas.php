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

// Obtener las ayudas ordenadas por fecha de creación
$sqlAyudas = "
SELECT a.Id, a.Nombre, a.Descripcion, a.Requisitos, a.Url, GROUP_CONCAT(c.Nombre SEPARATOR ', ') AS Categoria, GROUP_CONCAT(c.Id SEPARATOR ', ') AS CategoriaIds
FROM ayuda a
JOIN ayuda_categoria ac ON a.Id = ac.ayuda_id
JOIN categorias c ON ac.categoria_id = c.Id
GROUP BY a.Id
ORDER BY a.fecha_creacion DESC";
$resultAyudas = $conexion->query($sqlAyudas);

// Obtener las categorías disponibles
$sqlCategorias = "SELECT Id, Nombre FROM categorias";
$resultCategorias = $conexion->query($sqlCategorias);
$categorias = [];
if ($resultCategorias->num_rows > 0) {
    while ($categoria = $resultCategorias->fetch_assoc()) {
        $categorias[] = $categoria;
    }
}

// Manejar solicitud de creación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    if (!empty($_POST['nombre']) && !empty($_POST['descripcion']) && !empty($_POST['requisitos']) && !empty($_POST['url']) && !empty($_POST['categorias'])) {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $requisitos = $_POST['requisitos'];
        $url = $_POST['url'];
        $categorias = $_POST['categorias'];

        $sql = "INSERT INTO ayuda (Nombre, Descripcion, Requisitos, Url, fecha_creacion) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssss', $nombre, $descripcion, $requisitos, $url);

        if ($stmt->execute()) {
            $ayudaId = $stmt->insert_id;
            // Insertar relación de categoría
            $sqlCategoria = "INSERT INTO ayuda_categoria (ayuda_id, categoria_id) VALUES (?, ?)";
            $stmtCategoria = $conexion->prepare($sqlCategoria);
            foreach ($categorias as $categoria_id) {
                $stmtCategoria->bind_param('ii', $ayudaId, $categoria_id);
                $stmtCategoria->execute();
            }

            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
    exit();
}

// Manejar solicitud de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if (!empty($_POST['id']) && !empty($_POST['nombre']) && !empty($_POST['descripcion']) && !empty($_POST['requisitos']) && !empty($_POST['url']) && !empty($_POST['categorias'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $requisitos = $_POST['requisitos'];
        $url = $_POST['url'];
        $categorias = $_POST['categorias'];

        if (count($categorias) == 0) {
            echo 'error';
            exit();
        }

        $sql = "UPDATE ayuda SET Nombre = ?, Descripcion = ?, Requisitos = ?, Url = ? WHERE Id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssssi', $nombre, $descripcion, $requisitos, $url, $id);

        if ($stmt->execute()) {
            // Eliminar categorías existentes
            $sqlDeleteCategorias = "DELETE FROM ayuda_categoria WHERE ayuda_id = ?";
            $stmtDeleteCategorias = $conexion->prepare($sqlDeleteCategorias);
            $stmtDeleteCategorias->bind_param('i', $id);
            $stmtDeleteCategorias->execute();

            // Insertar nuevas categorías
            $sqlCategoria = "INSERT INTO ayuda_categoria (ayuda_id, categoria_id) VALUES (?, ?)";
            $stmtCategoria = $conexion->prepare($sqlCategoria);
            foreach ($categorias as $categoria_id) {
                $stmtCategoria->bind_param('ii', $id, $categoria_id);
                $stmtCategoria->execute();
            }

            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
    exit();
}

// Manejar solicitud de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];

        // Eliminar relación de categoría
        $sql = "DELETE FROM ayuda_categoria WHERE ayuda_id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Eliminar registros de favoritos
        $sql = "DELETE FROM favoritos WHERE id_ayuda = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Eliminar ayuda
        $sql = "DELETE FROM ayuda WHERE Id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);

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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <style>
        .url {
            min-width: 200px;
            max-width: 400px;
            word-wrap: break-word;
            word-break: break-all;
        }
    </style>
    <title>Moderar Ayudas</title>
</head>

<body>
    <?php include 'menuDash.php'; ?>
    <main class="mainDashboard">
        <div class="row">
            <div class="col-md-10 rightColumn">
                <section>
                    <h3>Moderar Ayudas</h3>
                    <button type="button" class="btn btn-outline-success btn-sm my-3" id="createButton">Crear Ayuda</button>
                    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar por nombre, descripción o requisitos">
                    <div class="table-responsive-xl">
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Requisitos</th>
                                    <th>URL</th>
                                    <th>Categorías</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="ayudasTableBody">
                                <?php
                                if ($resultAyudas->num_rows > 0) {
                                    while ($ayuda = $resultAyudas->fetch_assoc()) {
                                        $nombre = htmlspecialchars($ayuda['Nombre']);
                                        $descripcion = htmlspecialchars($ayuda['Descripcion']);
                                        $requisitos = htmlspecialchars($ayuda['Requisitos']);
                                        $url = htmlspecialchars($ayuda['Url']);
                                        $categoria = htmlspecialchars($ayuda['Categoria']);
                                        $categoriaIds = htmlspecialchars($ayuda['CategoriaIds']);

                                        echo "<tr data-id='{$ayuda['Id']}' data-categoria-ids='{$categoriaIds}'>";
                                        echo "<td class=\"url\">{$nombre}</td>";
                                        echo "<td class=\"url\">{$descripcion}</td>";
                                        echo "<td class=\"url\">{$requisitos}</td>";
                                        echo "<td class=\"url\">{$url}</td>";
                                        echo "<td>{$categoria}</td>";
                                        echo "<td><button type=\"button\" class=\"btn btn-outline-primary btn-sm edit-btn\">Editar</button></td>";
                                        echo "<td><button type=\"button\" class=\"btn btn-outline-danger btn-sm delete-btn\">Eliminar</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7'>No hay ayudas registradas.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- Modal para editar -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Editar Ayuda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <textarea class="form-control" id="editNombre" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="editDescripcion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editRequisitos" class="form-label">Requisitos</label>
                            <textarea class="form-control" id="editRequisitos" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editUrl" class="form-label">URL</label>
                            <textarea class="form-control" id="editUrl" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editCategorias" class="form-label">Categorías</label>
                            <?php foreach ($categorias as $categoria) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="<?= $categoria['Id'] ?>" id="edit-categoria-<?= $categoria['Id'] ?>">
                                    <label class="form-check-label" for="edit-categoria-<?= $categoria['Id'] ?>">
                                        <?= htmlspecialchars($categoria['Nombre']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="editId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear -->
    <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Crear Ayuda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createForm">
                        <div class="mb-3">
                            <label for="createNombre" class="form-label">Nombre</label>
                            <textarea class="form-control" id="createNombre" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createDescripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="createDescripcion" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createRequisitos" class="form-label">Requisitos</label>
                            <textarea class="form-control" id="createRequisitos" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createUrl" class="form-label">URL</label>
                            <textarea class="form-control" id="createUrl" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createCategorias" class="form-label">Categorías</label>
                            <?php foreach ($categorias as $categoria) : ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categorias[]" value="<?= $categoria['Id'] ?>" id="create-categoria-<?= $categoria['Id'] ?>">
                                    <label class="form-check-label" for="create-categoria-<?= $categoria['Id'] ?>">
                                        <?= htmlspecialchars($categoria['Nombre']) ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="saveCreate">Crear</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Crear filtro de búsqueda
            $('#search').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $.ajax({
                    url: 'searchAyudas.php',
                    type: 'GET',
                    data: {
                        search: searchText
                    },
                    success: function(response) {
                        $('#ayudasTableBody').html(response);
                    }
                });
            });

            // Create button click
            $('#createButton').on('click', function() {
                $('#createModal').modal('show');
            });

            // Save create button click
            $('#saveCreate').on('click', function() {
                var nombre = $('#createNombre').val();
                var descripcion = $('#createDescripcion').val();
                var requisitos = $('#createRequisitos').val();
                var url = $('#createUrl').val();
                var categorias = $('input[name="categorias[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                if (categorias.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe seleccionar al menos una categoría.'
                    });
                    return;
                }

                $.ajax({
                    url: 'moderarAyudas.php',
                    type: 'POST',
                    data: {
                        create: true,
                        nombre: nombre,
                        descripcion: descripcion,
                        requisitos: requisitos,
                        url: url,
                        categorias: categorias // Enviar como array
                    },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ayuda creada',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo crear la ayuda.'
                            });
                        }
                    }
                });
            });

            // Edit button click
            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var nombre = row.find('td').eq(0).text();
                var descripcion = row.find('td').eq(1).text();
                var requisitos = row.find('td').eq(2).text();
                var url = row.find('td').eq(3).text();
                var categoriaIds = row.data('categoria-ids').toString().split(',');

                $('#editId').val(id);
                $('#editNombre').val(nombre);
                $('#editDescripcion').val(descripcion);
                $('#editRequisitos').val(requisitos);
                $('#editUrl').val(url);

                // Marcar las categorías correspondientes
                $('input[name="categorias[]"]').each(function() {
                    var checkbox = $(this);
                    checkbox.prop('checked', categoriaIds.includes(checkbox.val()));
                });

                $('#editModal').modal('show');
            });

            // Save changes button click
            $('#saveChanges').on('click', function() {
                var id = $('#editId').val();
                var nombre = $('#editNombre').val();
                var descripcion = $('#editDescripcion').val();
                var requisitos = $('#editRequisitos').val();
                var url = $('#editUrl').val();
                var categorias = $('input[name="categorias[]"]:checked').map(function() {
                    return $(this).val();
                }).get();

                if (categorias.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe seleccionar al menos una categoría.'
                    });
                    return;
                }

                $.ajax({
                    url: 'moderarAyudas.php',
                    type: 'POST',
                    data: {
                        update: true,
                        id: id,
                        nombre: nombre,
                        descripcion: descripcion,
                        requisitos: requisitos,
                        url: url,
                        categorias: categorias // Enviar como array
                    },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Ayuda actualizada',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo actualizar la ayuda.'
                            });
                        }
                    }
                });
            });

            // Delete button click
            $('.delete-btn').on('click', function() {
                var row = $(this).closest('tr');
                var id = row.data('id');

                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminarlo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'moderarAyudas.php',
                            type: 'POST',
                            data: {
                                delete: true,
                                id: id
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    Swal.fire(
                                        '¡Eliminado!',
                                        'La ayuda ha sido eliminada.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error',
                                        'No se pudo eliminar la ayuda.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
