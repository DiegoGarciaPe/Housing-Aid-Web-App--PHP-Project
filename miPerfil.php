<?php
session_start();
require 'conexionBBDD.php';
$conexion = conexionBBDD();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['id_usuario'])) {
    echo "Error: Usuario no autenticado.";
    exit;
}

// Obtener ID del usuario de la sesión
$user_id = $_SESSION['id_usuario'];

$sql = "SELECT * FROM usuario WHERE id = $user_id";
$result = $conexion->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "No se encontró el usuario";
    exit;
}

$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include 'menuDash.php'; ?>

<main class="mainDashboard">
    <div class="row justify-content-left">
        <div class="col-md-10 rightColumn">
            <h3 class="mb-4">Editar Perfil</h3>
            <form id="profileForm" method="POST" action="actualizarPerfil.php" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="nombre" class="form-label h5">Nombre</label>
                    <input type="text" id="nombre" name="Nombre" class="form-control" value="<?php echo htmlspecialchars($user['Nombre']); ?>" required>
                    <div class="invalid-feedback">Por favor ingrese su nombre.</div>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label h5">Apellidos</label>
                    <input type="text" id="apellidos" name="Apellidos" class="form-control" value="<?php echo htmlspecialchars($user['Apellidos'] ?: ''); ?>" placeholder="Ingrese sus apellidos aquí..." required>
                    <div class="invalid-feedback">Por favor ingrese sus apellidos.</div>
                </div>
                <div class="mb-3">
                    <label for="direccion" class="form-label h5">Dirección</label>
                    <input type="text" id="direccion" name="Direccion" class="form-control" value="<?php echo htmlspecialchars($user['Direccion'] ?: ''); ?>" placeholder="Ingrese su dirección aquí..." required>
                    <div class="invalid-feedback">Por favor ingrese su dirección.</div>
                </div>
                <div class="mb-3">
                    <label for="estadoCivil" class="form-label h5">Estado civil</label>
                    <select id="estadoCivil" name="Estado_Civil" class="form-select" required>
                        <option value="soltero" <?php if ($user['Estado_Civil'] == 'soltero') echo 'selected'; ?>>Solter@</option>
                        <option value="casado" <?php if ($user['Estado_Civil'] == 'casado') echo 'selected'; ?>>Casad@</option>
                        <option value="viudo" <?php if ($user['Estado_Civil'] == 'viudo') echo 'selected'; ?>>Viud@</option>
                    </select>
                    <div class="invalid-feedback">Por favor seleccione su estado civil.</div>
                </div>
                <div class="mb-3">
                    <label for="fechaNacimiento" class="form-label h5">Fecha de nacimiento</label>
                    <input type="date" id="fechaNacimiento" name="Fecha_Nacimiento" class="form-control" value="<?php echo htmlspecialchars($user['Fecha_Nacimiento']); ?>" required>
                    <div class="invalid-feedback">Por favor ingrese su fecha de nacimiento.</div>
                </div>
                <div class="mb-3">
                    <label for="sexo" class="form-label h5">Sexo</label>
                    <select id="sexo" name="Sexo" class="form-select" required>
                        <option value="masculino" <?php if ($user['Sexo'] == 'masculino') echo 'selected'; ?>>Masculino</option>
                        <option value="femenino" <?php if ($user['Sexo'] == 'femenino') echo 'selected'; ?>>Femenino</option>
                        <option value="otro" <?php if ($user['Sexo'] == 'otro') echo 'selected'; ?>>Otro</option>
                    </select>
                    <div class="invalid-feedback">Por favor seleccione su sexo.</div>
                </div>
                <div class="mb-3">
                    <label for="nacionalidad" class="form-label h5">Nacionalidad</label>
                    <input type="text" id="nacionalidad" name="Nacionalidad" class="form-control" value="<?php echo htmlspecialchars($user['Nacionalidad'] ?: ''); ?>" list="nacionalidades" placeholder="Ingrese su nacionalidad aquí..." required>
                    <datalist id="nacionalidades">
                        <!-- Lista de 100 nacionalidades principales -->
                        <option value="Afganistán"><option value="Alemania"><option value="Arabia Saudita"><option value="Argentina"><option value="Australia"><option value="Bangladés"><option value="Brasil"><option value="Canadá"><option value="Chile"><option value="China"><option value="Colombia"><option value="Corea del Sur"><option value="Egipto"><option value="Emiratos Árabes Unidos"><option value="España"><option value="Estados Unidos"><option value="Etiopía"><option value="Filipinas"><option value="Francia"><option value="Ghana"><option value="Grecia"><option value="India"><option value="Indonesia"><option value="Irán"><option value="Irlanda"><option value="Israel"><option value="Italia"><option value="Japón"><option value="Kenia"><option value="Malasia"><option value="Marruecos"><option value="México"><option value="Nigeria"><option value="Pakistán"><option value="Países Bajos"><option value="Perú"><option value="Polonia"><option value="Portugal"><option value="Reino Unido"><option value="República Checa"><option value="República Dominicana"><option value="Rusia"><option value="Sudáfrica"><option value="Suecia"><option value="Suiza"><option value="Tailandia"><option value="Turquía"><option value="Ucrania"><option value="Uganda"><option value="Venezuela">
                    </datalist>
                    <div class="invalid-feedback">Por favor ingrese su nacionalidad.</div>
                </div>
                <input type="hidden" id="originalEmail" value="<?php echo htmlspecialchars($user['Email']); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label h5">Email</label>
                    <input type="email" id="email" name="Email" class="form-control" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                    <div class="invalid-feedback">Por favor ingrese su email.</div>
                </div>
                <div class="mb-3">
                    <label for="profesion" class="form-label h5">Profesión</label>
                    <input type="text" id="profesion" name="Profesion" class="form-control" value="<?php echo htmlspecialchars($user['Profesion'] ?: ''); ?>" placeholder="Ingrese su profesión aquí..." required>
                    <div class="invalid-feedback">Por favor ingrese su profesión.</div>
                </div>
                <div class="mb-3">
                    <label for="discapacidad" class="form-label h5">Discapacidad</label>
                    <select id="discapacidad" name="Discapacidad" class="form-select" required>
                        <option value="no" <?php if ($user['Discapacidad'] == 'no') echo 'selected'; ?>>No</option>
                        <option value="menos33" <?php if ($user['Discapacidad'] == 'menos33') echo 'selected'; ?>>Sí, menos del 33%</option>
                        <option value="mas33" <?php if ($user['Discapacidad'] == 'mas33') echo 'selected'; ?>>Sí, más del 33%</option>
                    </select>
                    <div class="invalid-feedback">Por favor seleccione una opción de discapacidad.</div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" id="parado" name="Parado" class="form-check-input" <?php if ($user['Parado']) echo 'checked'; ?>>
                    <label for="parado" class="form-check-label h5">Desempleado</label>
                </div>
                <div class="mb-3" id="desempleoOptions" style="display: none;">
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Cobrando prestación por desempleo</label>
                        <div>
                            <input type="radio" id="prestacionSi" name="Prestacion_Desempleo" value="1" class="form-check-input" <?php if ($user['Prestacion_Desempleo']) echo 'checked'; ?>>
                            <label for="prestacionSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="prestacionNo" name="Prestacion_Desempleo" value="0" class="form-check-input" <?php if (!$user['Prestacion_Desempleo']) echo 'checked'; ?>>
                            <label for="prestacionNo" class="form-check-label">No</label>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Cobrando subsidio por desempleo</label>
                        <div>
                            <input type="radio" id="subsidioSi" name="Subsidio_Desempleo" value="1" class="form-check-input" <?php if ($user['Subsidio_Desempleo']) echo 'checked'; ?>>
                            <label for="subsidioSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="subsidioNo" name="Subsidio_Desempleo" value="0" class="form-check-input" <?php if (!$user['Subsidio_Desempleo']) echo 'checked'; ?>>
                            <label for="subsidioNo" class="form-check-label">No</label>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Paro agotado</label>
                        <div>
                            <input type="radio" id="paroSi" name="Paro_Cobrado_Agotado" value="1" class="form-check-input" <?php if ($user['Paro_Cobrado_Agotado']) echo 'checked'; ?>>
                            <label for="paroSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="paroNo" name="Paro_Cobrado_Agotado" value="0" class="form-check-input" <?php if (!$user['Paro_Cobrado_Agotado']) echo 'checked'; ?>>
                            <label for="paroNo" class="form-check-label">No</label>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Subsidio agotado</label>
                        <div>
                            <input type="radio" id="subsidioAgotadoSi" name="Subsidio_Cobrado_Agotado" value="1" class="form-check-input" <?php if ($user['Subsidio_Cobrado_Agotado']) echo 'checked'; ?>>
                            <label for="subsidioAgotadoSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="subsidioAgotadoNo" name="Subsidio_Cobrado_Agotado" value="0" class="form-check-input" <?php if (!$user['Subsidio_Cobrado_Agotado']) echo 'checked'; ?>>
                            <label for="subsidioAgotadoNo" class="form-check-label">No</label>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Derecho a paro</label>
                        <div>
                            <input type="radio" id="derechoParoSi" name="Derecho_Paro" value="1" class="form-check-input" <?php if ($user['Derecho_Paro']) echo 'checked'; ?>>
                            <label for="derechoParoSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="derechoParoNo" name="Derecho_Paro" value="0" class="form-check-input" <?php if (!$user['Derecho_Paro']) echo 'checked'; ?>>
                            <label for="derechoParoNo" class="form-check-label">No</label>
                        </div>
                    </div>
                    <div class="form-check mb-2">
                        <label class="form-check-label h6">Derecho a subsidio</label>
                        <div>
                            <input type="radio" id="derechoSubsidioSi" name="Derecho_Subsidio" value="1" class="form-check-input" <?php if ($user['Derecho_Subsidio']) echo 'checked'; ?>>
                            <label for="derechoSubsidioSi" class="form-check-label">Sí</label>
                        </div>
                        <div>
                            <input type="radio" id="derechoSubsidioNo" name="Derecho_Subsidio" value="0" class="form-check-input" <?php if (!$user['Derecho_Subsidio']) echo 'checked'; ?>>
                            <label for="derechoSubsidioNo" class="form-check-label">No</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-outline-success">Actualizar</button>
            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
