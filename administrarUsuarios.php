<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$nombreUsuario = $_SESSION['nombre'];
$emailUsuario = $_SESSION['email'];

$sql = "SELECT id FROM usuario WHERE email = '$emailUsuario'";
$result = $conexion->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $idUsuario = $row['id'];
} else {
    echo "Error: Usuario no encontrado.";
    exit();
}

$sqlUsuarios = "SELECT u.id, u.Nombre, u.Email, su.admin_global, su.admin_contenido, su.trabajador_social
                FROM usuario u
                LEFT JOIN superusuario su ON u.id = su.id_usuario";
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
    <title>Dashboard Usuario</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php require 'menuDash.php'; ?>
    <main class="mainDashboard">
        <div class="row">
            <div class="col-md-10 rightColumn">
                <section>
                    <h3>Administrador de Usuarios</h3>
                    <input type="text" id="search" class="form-control" placeholder="Buscar por nombre, apellido o email" onkeyup="filterUsers()">
                    <form id="rolesForm" method="post" action="updateRoles.php">
                        <div class="table-responsive">
                            <table class="table table-striped mt-3">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Admin Global</th>
                                        <th>Admin Contenido</th>
                                        <th>Trabajador Social</th>
                                    </tr>
                                </thead>
                                <tbody id="usersTable">
                                    <?php
                                    if ($resultUsuarios->num_rows > 0) {
                                        while ($user = $resultUsuarios->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($user['Nombre']) . "</td>";
                                            echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
                                            echo "<input type='hidden' name='user_id[]' value='{$user['id']}'>";
                                            echo "<td><input type='checkbox' name='admin_global[]' value='{$user['id']}' class='role-checkbox' data-user-id='{$user['id']}' data-role='admin_global' " . ($user['admin_global'] ? 'checked' : '') . "></td>";
                                            echo "<td><input type='checkbox' name='admin_contenido[]' value='{$user['id']}' class='role-checkbox' data-user-id='{$user['id']}' data-role='admin_contenido' " . ($user['admin_contenido'] ? 'checked' : '') . "></td>";
                                            echo "<td><input type='checkbox' name='trabajador_social[]' value='{$user['id']}' class='role-checkbox' data-user-id='{$user['id']}' data-role='trabajador_social' " . ($user['trabajador_social'] ? 'checked' : '') . "></td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.role-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                let userId = this.getAttribute('data-user-id');
                let role = this.getAttribute('data-role');
                let checkboxes = document.querySelectorAll(`.role-checkbox[data-user-id='${userId}']`);

                if (this.checked) {
                    checkboxes.forEach(function(cb) {
                        if (cb.getAttribute('data-role') !== role) {
                            cb.checked = false;
                        }
                    });
                }
            });
        });

        function filterUsers() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("search");
            filter = input.value.toLowerCase();
            table = document.getElementById("usersTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (var j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }
    </script>
</body>

</html>