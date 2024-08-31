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

$sql = "SELECT Id FROM usuario WHERE email = '$emailUsuario'";
$result = $conexion->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUsuario = $row['Id'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

// Obtener las consultas del usuario
$sqlEnlaces = "SELECT * FROM enlaces";
$resultEnlaces = $conexion->query($sqlEnlaces);

// Maneja la petición de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    if (!empty($_POST['id']) && !empty($_POST['nombre']) && !empty($_POST['enlace'])) {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $enlace = $_POST['enlace'];

        $sql = "UPDATE enlaces SET nombre = ?, enlace = ? WHERE Id = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ssi', $nombre, $enlace, $id);

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

// Maneja la petición de eliminación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    if (!empty($_POST['id'])) {
        $id = $_POST['id'];

        $sql = "DELETE FROM enlaces WHERE Id = ?";
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

// Maneja la petición de creación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create'])) {
    if (!empty($_POST['nombre']) && !empty($_POST['enlace'])) {
        $nombre = $_POST['nombre'];
        $enlace = $_POST['enlace'];

        $sql = "INSERT INTO enlaces (nombre, enlace) VALUES (?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('ss', $nombre, $enlace);

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
    <title>Moderar Enlaces</title>
</head>

<body>
    <?php include 'menuDash.php'; ?>
    <main class="mainDashboard">
        <div class="row">
            <div class="col-md-10 rightColumn">
                <section>
                    <h3>Moderar Enlaces</h3>
                    <button type="button" class="btn btn-outline-success btn-sm" id="createButton">Crear Enlace</button>
                    <div class="table-responsive">
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>URL</th>
                                    <th>Editar</th>
                                    <th>Eliminar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultEnlaces->num_rows > 0) {
                                    while ($user = $resultEnlaces->fetch_assoc()) {
                                        $nombre = $user['nombre'] ? htmlspecialchars($user['nombre']) : 'No ingresado';
                                        $enlace = $user['enlace'] ? htmlspecialchars($user['enlace']) : 'No ingresado';

                                        echo "<tr data-id='{$user['Id']}'>";
                                        echo "<td class=\"url\">{$nombre}</td>";
                                        echo "<td class=\"url\">{$enlace}</td>";
                                        echo "<td><button type=\"button\" class=\"btn btn-outline-primary btn-sm edit-btn\">Editar</button></td>";
                                        echo "<td><button type=\"button\" class=\"btn btn-outline-danger btn-sm delete-btn\">Eliminar</button></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No hay enlaces registrados.</td></tr>";
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
                    <h5 class="modal-title" id="editModalLabel">Editar Enlace</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <div class="mb-3">
                            <label for="editNombre" class="form-label">Nombre</label>
                            <textarea class="form-control" id="editNombre" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editEnlace" class="form-label">Enlace</label>
                            <textarea class="form-control" id="editEnlace" rows="2"></textarea>
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
                    <h5 class="modal-title" id="createModalLabel">Crear Enlace</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createForm">
                        <div class="mb-3">
                            <label for="createNombre" class="form-label">Nombre</label>
                            <textarea class="form-control" id="createNombre" rows="1"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="createEnlace" class="form-label">Enlace</label>
                            <textarea class="form-control" id="createEnlace" rows="2"></textarea>
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
            // Edit button click
            $('.edit-btn').on('click', function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                var nombre = row.find('td').eq(0).text();
                var enlace = row.find('td').eq(1).text();

                $('#editId').val(id);
                $('#editNombre').val(nombre);
                $('#editEnlace').val(enlace);

                $('#editModal').modal('show');
            });

            // Save changes button click
            $('#saveChanges').on('click', function() {
                var id = $('#editId').val();
                var nombre = $('#editNombre').val();
                var enlace = $('#editEnlace').val();

                $.ajax({
                    url: 'moderarEnlaces.php',
                    type: 'POST',
                    data: {
                        update: true,
                        id: id,
                        nombre: nombre,
                        enlace: enlace
                    },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Enlace actualizado',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo actualizar el enlace.'
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
                            url: 'moderarEnlaces.php',
                            type: 'POST',
                            data: {
                                delete: true,
                                id: id
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    Swal.fire(
                                        '¡Eliminado!',
                                        'El enlace ha sido eliminado.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Error',
                                        'No se pudo eliminar el enlace.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });

            // Pulsar el botón crear
            $('#createButton').on('click', function() {
                $('#createModal').modal('show');
            });

            // Pulsar el botón guardar dentro de crear
            $('#saveCreate').on('click', function() {
                var nombre = $('#createNombre').val();
                var enlace = $('#createEnlace').val();

                $.ajax({
                    url: 'moderarEnlaces.php',
                    type: 'POST',
                    data: {
                        create: true,
                        nombre: nombre,
                        enlace: enlace
                    },
                    success: function(response) {
                        if (response === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Enlace creado',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo crear el Enlace.'
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>