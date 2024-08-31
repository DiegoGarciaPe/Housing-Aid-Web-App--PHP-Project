<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$sqlUsuarios = "SELECT id, Nombre, Apellidos, Direccion, Email, Nacionalidad FROM usuario";
$resultUsuarios = $conexion->query($sqlUsuarios);
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
    <title>Usuarios Registrados</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php require 'menuDash.php'; ?>
    <main class="mainDashboard">
        <div class="row">
            <div class="col-md-10 rightColumn">
                <section>
                    <h3>Usuarios Registrados</h3>
                    <input type="text" id="search" class="form-control mb-3" placeholder="Buscar por nombre, apellidos o email" onkeyup="filterUsers()">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Dirección</th>
                                    <th>Email</th>
                                    <th>Nacionalidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($resultUsuarios->num_rows > 0) {
                                    while ($user = $resultUsuarios->fetch_assoc()) {
                                        $nombre = $user['Nombre'] ? htmlspecialchars($user['Nombre']) : 'No ingresado';
                                        $apellidos = $user['Apellidos'] ? htmlspecialchars($user['Apellidos']) : 'No ingresado';
                                        $direccion = $user['Direccion'] ? htmlspecialchars($user['Direccion']) : 'No ingresado';
                                        $email = $user['Email'] ? htmlspecialchars($user['Email']) : 'No ingresado';
                                        $nacionalidad = $user['Nacionalidad'] ? htmlspecialchars($user['Nacionalidad']) : 'No ingresado';

                                        echo "<tr data-id='{$user['id']}' class='user-row' style='cursor: pointer;'>";
                                        echo "<td>{$nombre}</td>";
                                        echo "<td>{$apellidos}</td>";
                                        echo "<td>{$direccion}</td>";
                                        echo "<td>{$email}</td>";
                                        echo "<td>{$nacionalidad}</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Información del Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="modalNombre"></span></p>
                    <p><strong>Apellidos:</strong> <span id="modalApellidos"></span></p>
                    <p><strong>Sexo:</strong> <span id="modalSexo"></span></p>
                    <p><strong>Dirección:</strong> <span id="modalDireccion"></span></p>
                    <p><strong>Fecha de Nacimiento:</strong> <span id="modalFechaNacimiento"></span></p>
                    <p><strong>Nacionalidad:</strong> <span id="modalNacionalidad"></span></p>
                    <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                    <p><strong>Profesión:</strong> <span id="modalProfesion"></span></p>
                    <p><strong>Discapacidad:</strong> <span id="modalDiscapacidad"></span></p>
                    <p><strong>Parado:</strong> <span id="modalParado"></span></p>
                    <p><strong>Estado Civil:</strong> <span id="modalEstadoCivil"></span></p>
                    <p><strong>Prestación de Desempleo:</strong> <span id="modalPrestacionDesempleo"></span></p>
                    <p><strong>Subsidio de Desempleo:</strong> <span id="modalSubsidioDesempleo"></span></p>
                    <p><strong>Paro Cobrado Agotado:</strong> <span id="modalParoCobradoAgotado"></span></p>
                    <p><strong>Derecho a Paro:</strong> <span id="modalDerechoParo"></span></p>
                    <p><strong>Derecho a Subsidio:</strong> <span id="modalDerechoSubsidio"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            $('.user-row').click(function() {
                var userId = $(this).data('id');

                $.ajax({
                    url: 'getUserInfo.php',
                    type: 'GET',
                    data: {
                        id: userId
                    },
                    success: function(data) {
                        var user = JSON.parse(data);
                        $('#modalNombre').text(user.Nombre || 'No ingresado');
                        $('#modalApellidos').text(user.Apellidos || 'No ingresado');
                        $('#modalSexo').text(user.Sexo || 'No ingresado');
                        $('#modalDireccion').text(user.Direccion || 'No ingresado');
                        $('#modalFechaNacimiento').text(user.Fecha_Nacimiento || 'No ingresado');
                        $('#modalNacionalidad').text(user.Nacionalidad || 'No ingresado');
                        $('#modalEmail').text(user.Email || 'No ingresado');
                        $('#modalProfesion').text(user.Profesion || 'No ingresado');
                        $('#modalDiscapacidad').text(user.Discapacidad || 'No ingresado');
                        $('#modalParado').text(user.Parado || 'No ingresado');
                        $('#modalEstadoCivil').text(user.Estado_Civil || 'No ingresado');
                        $('#modalPrestacionDesempleo').text(user.Prestacion_Desempleo || 'No ingresado');
                        $('#modalSubsidioDesempleo').text(user.Subsidio_Desempleo || 'No ingresado');
                        $('#modalParoCobradoAgotado').text(user.Paro_Cobrado_Agotado || 'No ingresado');
                        $('#modalDerechoParo').text(user.Derecho_Paro || 'No ingresado');
                        $('#modalDerechoSubsidio').text(user.Derecho_Subsidio || 'No ingresado');

                        $('#userModal').modal('show');
                    }
                });
            });

            $('#search').on('input', function() {
                var searchText = $(this).val().toLowerCase();

                $('.user-row').each(function() {
                    var $row = $(this);
                    var found = false;

                    $row.find('td').each(function() {
                        var cellText = $(this).text().toLowerCase();

                        if (cellText.includes(searchText)) {
                            found = true;
                            return false;
                        }
                    });

                    if (found) {
                        $row.show();
                    } else {
                        $row.hide();
                    }
                });
            });
        });
    </script>
</body>

</html>